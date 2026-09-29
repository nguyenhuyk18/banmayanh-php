"""Local end-to-end smoke test. Creates and removes only its own test records."""
import hashlib
import datetime
import base64
import pathlib
import http.cookiejar
import json
import re
import secrets
import subprocess
import urllib.error
import urllib.parse
import urllib.request

BASE = 'http://localhost/backend/godashop/'
MYSQL = 'C:/xampp/mysql/bin/mysql.exe'
DB = 'godashop_recovered_20260919'
tag = secrets.token_hex(7)
email = f'codex-smoke-{tag}@example.invalid'
username = f'codex_{tag}'
password = secrets.token_urlsafe(16)
customer_id = staff_id = order_id = None
admin_order_id = None
product_id = None
temp_category_id = temp_brand_id = None
article_id = None

def sql(query):
    result = subprocess.run([MYSQL, '-u', 'root', '-N', '-B', DB, '-e', query], capture_output=True, text=True)
    if result.returncode: raise RuntimeError(result.stderr)
    return result.stdout.strip()

def client():
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), urllib.request.HTTPRedirectHandler())

def request(opener, path, data=None, csrf=None, ajax=True):
    headers = {'X-Requested-With': 'XMLHttpRequest'} if ajax and path.startswith('index.php?c=cart&') else {}
    if csrf: headers['X-CSRF-Token'] = csrf
    payload = urllib.parse.urlencode(data).encode() if data is not None else None
    try:
        response = opener.open(urllib.request.Request(BASE + path, payload, headers), timeout=12)
        return response.status, response.url, response.read().decode('utf-8', 'replace')
    except urllib.error.HTTPError as error:
        return error.code, error.url, error.read().decode('utf-8', 'replace')

def multipart(opener, path, fields, filename, content):
    boundary = 'codex' + secrets.token_hex(10)
    chunks = []
    for key, value in fields.items():
        chunks.append(('--'+boundary+'\r\nContent-Disposition: form-data; name="'+key+'"\r\n\r\n'+str(value)+'\r\n').encode())
    chunks.append(('--'+boundary+'\r\nContent-Disposition: form-data; name="image"; filename="'+filename+'"\r\nContent-Type: image/png\r\n\r\n').encode()+content+b'\r\n')
    chunks.append(('--'+boundary+'--\r\n').encode())
    req = urllib.request.Request(BASE+path, b''.join(chunks), {'Content-Type':'multipart/form-data; boundary='+boundary})
    try:
        response = opener.open(req, timeout=12)
        return response.status, response.url, response.read().decode('utf-8','replace')
    except urllib.error.HTTPError as error:
        return error.code, error.url, error.read().decode('utf-8','replace')

def token(html):
    match = re.search(r'name="_csrf" value="([a-f0-9]+)"', html)
    if not match: raise AssertionError('CSRF token absent')
    return match.group(1)

def check(name, condition):
    if not condition: raise AssertionError(name)
    print('PASS', name)

try:
    site = client()
    status, _, home = request(site, '')
    csrf = token(home)
    check('home and session token', status == 200 and 'stylesheet' in home and 'Sản phẩm bán chạy' in home)
    for path in ['san-pham.html', 'gio-hang.html', 'lien-he.html', 'gioi-thieu.html', 'bai-viet.html', 'san-pham/san-pham-12.html', 'chinh-sach-doi-tra.html', 'index.php?c=product&a=index&sort=price-asc', 'index.php?c=product&a=index&search=kem']:
        code, _, html = request(site, path)
        check(path, code == 200 and 'Fatal error' not in html)
    code, _, html = request(site, 'index.php?c=cart&a=add', {'product_id':2,'qty':1})
    check('cart rejects missing token', code == 403)
    code, _, html = request(site, 'index.php?c=cart&a=add', {'product_id':2,'qty':1}, csrf)
    check('cart add', code == 200 and json.loads(html)['total_product_number'] == 1)
    code, _, html = request(site, 'index.php?c=cart&a=index')
    check('cart read', code == 200 and json.loads(html)['items']['2']['qty'] == 1)
    code, _, html = request(site, 'index.php?c=cart&a=update', {'product_id':2,'qty':2}, csrf)
    check('cart update', code == 200 and json.loads(html)['total_product_number'] == 2)
    code, _, html = request(site, 'index.php?c=cart&a=update', {'product_id':2,'qty':99999}, csrf)
    check('stock validation', code == 422)
    code, _, html = request(site, 'index.php?c=cart&a=delete', {'product_id':2}, csrf)
    check('cart delete', code == 200 and json.loads(html)['total_product_number'] == 0)
    code, url, html = request(site, 'index.php?c=cart&a=add', {'product_id':2,'qty':1,'_csrf':csrf}, ajax=False)
    check('add-to-cart HTML form', code == 200 and 'Đã thêm sản phẩm vào giỏ hàng' in html and 'number-total-product">1<' in html)
    code, _, html = request(site, 'gio-hang.html')
    check('cart page shows selected product', code == 200 and 'Beaumore Secret Whitening Cream' in html and 'Cập nhật' in html)
    code, _, _ = request(site, 'index.php?c=cart&a=delete', {'product_id':2,'_csrf':csrf}, ajax=False)
    check('remove-from-cart HTML form', code == 200)
    code, url, html = request(site, 'index.php?c=contact&a=subscribe', {'email':email,'_csrf':csrf})
    check('newsletter subscription', code == 200 and sql("SELECT email FROM newsletter WHERE email='%s'" % email) == email)

    # Use a dedicated customer to exercise the real login and checkout routes.
    bcrypt = subprocess.run(['C:/xampp/php/php.exe','-r', 'echo password_hash($argv[1], PASSWORD_DEFAULT);',password], capture_output=True, text=True, check=True).stdout
    sql("INSERT INTO customer (name,password,mobile,email,login_by,shipping_name,shipping_mobile,ward_id,housenumber_street,is_active) VALUES ('Smoke', '%s', '0900000000', '%s', 'form', 'Smoke','0900000000',NULL,'Test',1)" % (bcrypt,email))
    customer_id = int(sql("SELECT id FROM customer WHERE email='%s'" % email))
    code, url, _ = request(site, 'index.php?c=auth&a=login', {'email':email,'password':password,'_csrf':csrf})
    check('customer login', code == 200 and 'c=customer&a=show' in url)
    code, url, _ = request(site, 'index.php?c=customer&a=updateAccount', {'_csrf':csrf,'fullname':'Smoke Updated','mobile':'0900000000','current_password':'','password':''})
    check('customer profile update', code == 200 and sql('SELECT name FROM customer WHERE id=%d' % customer_id) == 'Smoke Updated')
    code, url, _ = request(site, 'index.php?c=customer&a=updateShippingDefault', {'_csrf':csrf,'fullname':'Smoke Updated','mobile':'0900000000','province':'01','district':'001','ward':'00001','address':'Test address'})
    check('customer address update', code == 200 and sql('SELECT ward_id FROM customer WHERE id=%d' % customer_id) == '00001')
    code, _, html = request(site, 'index.php?c=cart&a=add', {'product_id':2,'qty':1}, csrf)
    check('cart after login', code == 200)
    code, _, html = request(site, 'index.php?c=payment&a=checkout')
    checkout = re.search(r'name="checkout_token" value="([a-f0-9]+)"', html)
    check('checkout page', code == 200 and bool(checkout))
    if 'value="2" disabled' in html:
        code, _, _ = request(site, 'index.php?c=payment&a=order', {'_csrf':csrf,'checkout_token':checkout.group(1),'fullname':'Smoke','mobile':'0900000000','province':'01','district':'001','ward':'00001','address':'Test','payment_method':'2'})
        check('Stripe without key fails before order creation', code == 422 and not sql('SELECT id FROM `order` WHERE customer_id=%d' % customer_id))
    else:
        check('Stripe option enabled', 'value="2"' in html)
    code, _, html = request(site, 'index.php?c=payment&a=order', {'_csrf':csrf,'checkout_token':checkout.group(1),'fullname':'Smoke','mobile':'0900000000','province':'01','district':'001','ward':'00001','address':'Test','payment_method':'0'})
    order_id_text = sql('SELECT id FROM `order` WHERE customer_id=%d ORDER BY id DESC LIMIT 1' % customer_id)
    order_id = int(order_id_text) if order_id_text else None
    check('place order', code == 200 and bool(order_id))
    code, _, html = request(site, 'index.php?c=customer&a=orderDetail&id=%d' % order_id)
    check('order ownership', code == 200 and 'Smoke' in html)
    code, _, html = request(site, 'index.php?c=payment&a=order', {'_csrf':csrf,'checkout_token':checkout.group(1)})
    check('duplicate order blocked', code == 422)

    amount = int(sql('SELECT SUM(total_price) FROM order_item WHERE order_id=%d' % order_id)) + int(sql('SELECT shipping_fee FROM `order` WHERE id=%d' % order_id))
    session_id = 'cs_test_smoke'+tag
    sql('UPDATE `order` SET payment_method=2 WHERE id=%d' % order_id)
    sql("INSERT INTO stripe_payment (order_id,session_id,amount,currency,status) VALUES (%d,'%s',%d,'vnd','pending')" % (order_id,session_id,amount))
    php = "require 'config.php'; require 'connectDB.php'; require 'bootstrap.php'; $s=['id'=>$argv[1],'metadata'=>['order_id'=>$argv[2]],'amount_total'=>(int)$argv[3],'currency'=>'vnd','payment_status'=>'paid']; echo StripeService::reconcile($s),',',StripeService::reconcile($s);"
    result = subprocess.run(['C:/xampp/php/php.exe','-r',php,session_id,str(order_id),str(amount)],capture_output=True,text=True)
    check('Stripe payment reconciliation is idempotent', result.returncode == 0 and result.stdout.strip() == 'paid,paid' and sql('SELECT status FROM stripe_payment WHERE order_id=%d' % order_id) == 'paid')
    code, _, html = request(site, 'index.php?c=customer&a=orders')
    check('paid Stripe status shown to customer', code == 200 and 'Đã thanh toán' in html)

    admin = client()
    code, _, html = request(admin, 'admin/login.php')
    admin_csrf = token(html)
    sql("INSERT INTO staff (role_id,name,mobile,username,password,email,is_active) VALUES (1,'Smoke Admin','0900000000','%s','%s','%s',1)" % (username, hashlib.md5(password.encode()).hexdigest(),email))
    staff_id = int(sql("SELECT id FROM staff WHERE username='%s'" % username))
    code, url, html = request(admin, 'admin/index.php?c=auth&a=login', {'username':username,'password':password,'remember-me':'1','_csrf':admin_csrf})
    check('admin login and dashboard', code == 200 and 'admin/index.php' in url and 'Fatal error' not in html)
    code, url, _ = request(admin, 'admin/index.php?c=customer&a=update', {'_csrf':admin_csrf,'id':customer_id,'fullname':'Smoke Admin Edit','email':email,'mobile':'0900000000','shipping_name':'Smoke','shipping_mobile':'0900000000','ward':'00001','housenumber_street':'Test','active':'1'})
    check('admin customer update', code == 200 and sql('SELECT name FROM customer WHERE id=%d' % customer_id) == 'Smoke Admin Edit')
    code, _, _ = request(admin, 'admin/index.php?c=article&a=save', {'_csrf':admin_csrf,'title':'Smoke Article '+tag,'summary':'Test article','content':'Test content','is_published':'1'})
    article_text = sql("SELECT id FROM article WHERE slug='smoke-article-%s'" % tag)
    article_id = int(article_text) if article_text else None
    check('admin publish article', code == 200 and bool(article_id))
    code, _, html = request(site, 'bai-viet/smoke-article-%s-%d.html' % (tag,article_id))
    check('public article detail', code == 200 and 'Test content' in html)
    code, _, _ = request(admin, 'admin/index.php?c=article&a=update', {'_csrf':admin_csrf,'id':article_id,'title':'Smoke Article Updated '+tag,'summary':'Changed','content':'Updated content','is_published':'1'})
    check('admin edit article', code == 200 and sql('SELECT summary FROM article WHERE id=%d' % article_id) == 'Changed')
    code, _, _ = request(admin, 'admin/index.php?c=article&a=delete&id=%d&_csrf=%s' % (article_id,admin_csrf))
    check('admin delete article', code == 200 and not sql('SELECT id FROM article WHERE id=%d' % article_id))
    article_id = None
    for module in ('category','brand'):
        code, _, _ = request(admin, 'admin/index.php?c='+module+'&a=save', {'_csrf':admin_csrf,'name':'Smoke '+module+' '+tag})
        record_id_text = sql("SELECT id FROM %s WHERE name='Smoke %s %s'" % (module,module,tag))
        record_id = int(record_id_text) if record_id_text else None
        if module == 'category': temp_category_id = record_id
        else: temp_brand_id = record_id
        check('admin '+module+' create', code == 200 and bool(record_id))
        code, _, _ = request(admin, 'admin/index.php?c='+module+'&a=update', {'_csrf':admin_csrf,'id':record_id,'name':'Smoke Edited '+module+' '+tag})
        check('admin '+module+' edit', code == 200 and sql('SELECT name FROM %s WHERE id=%d' % (module,record_id)) == 'Smoke Edited '+module+' '+tag)
        code, _, _ = request(admin, 'admin/index.php?c='+module+'&a=delete&id=%d&_csrf=%s' % (record_id,admin_csrf))
        check('admin '+module+' delete', code == 200 and not sql('SELECT id FROM %s WHERE id=%d' % (module,record_id)))
        if module == 'category': temp_category_id = None
        else: temp_brand_id = None
    for module in ['product','category','brand','customer','order','staff','comment','newsletter','shippingfee','status']:
        code, _, html = request(admin, 'admin/index.php?c='+module+'&a=list')
        check('admin '+module, code == 200 and 'Fatal error' not in html and 'Không thể xử lý' not in html)
    for path in ['admin/index.php?c=customer&a=edit&id=%d' % customer_id,
                 'admin/index.php?c=product&a=edit&id=2',
                 'admin/index.php?c=order&a=add',
                 'admin/index.php?c=permission&a=listRole']:
        code, _, html = request(admin, path)
        check(path, code == 200 and 'Không thể xử lý' not in html and 'Fatal error' not in html)
    stock_before_admin_order = int(sql('SELECT inventory_qty FROM product WHERE id=2'))
    code, _, html = request(admin, 'admin/index.php?c=order&a=save', {
        '_csrf':admin_csrf,'status':'1','staff':str(staff_id),'customer':str(customer_id),
        'shipping_name':'Smoke','shipping_mobile':'0900000000','payment_method':'0',
        'ward':'00001','housenumber_street':'Test','shipping_fee':'999999',
        'delivered_date':datetime.date.today().isoformat(),
        'product_ids[]':'2','qties[]':'1'
    })
    admin_order_text = sql('SELECT id FROM `order` WHERE customer_id=%d AND staff_id=%d ORDER BY id DESC LIMIT 1' % (customer_id,staff_id))
    admin_order_id = int(admin_order_text) if admin_order_text else None
    check('admin create order', code == 200 and bool(admin_order_id))
    code, _, _ = request(admin, 'admin/index.php?c=order&a=update', {'_csrf':admin_csrf,'order_id':admin_order_id,'status':'2','staff_id':staff_id})
    admin_status = sql('SELECT order_status_id FROM `order` WHERE id=%d' % admin_order_id)
    check('admin update order status', code == 200 and admin_status == '2')
    code, _, _ = request(admin, 'admin/index.php?c=order&a=update', {'_csrf':admin_csrf,'order_id':admin_order_id,'status':'6','staff_id':staff_id})
    check('admin cancel restores stock', code == 200 and sql('SELECT order_status_id FROM `order` WHERE id=%d' % admin_order_id) == '6' and int(sql('SELECT inventory_qty FROM product WHERE id=2')) == stock_before_admin_order)
    code, _, html = request(admin, 'admin/index.php?c=order&a=delete&order_id=%d&_csrf=%s' % (admin_order_id,admin_csrf))
    check('admin delete canceled order without double stock', code == 200 and not sql('SELECT id FROM `order` WHERE id=%d' % admin_order_id) and int(sql('SELECT inventory_qty FROM product WHERE id=2')) == stock_before_admin_order)
    admin_order_id = None
    category_id = sql('SELECT id FROM category LIMIT 1')
    brand_id = sql('SELECT id FROM brand LIMIT 1')
    png = base64.b64decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aB1sAAAAASUVORK5CYII=')
    code, _, html = multipart(admin, 'admin/index.php?c=product&a=save', {
        '_csrf':admin_csrf,'name':'Smoke Product '+tag,'barcode':'SMOKE'+tag,
        'sku':'SMOKE'+tag,'price':'10000','discount_percentage':'0',
        'discount_from_date':'','discount_to_date':'','inventory_qty':'4',
        'description':'Smoke test','featured':'0','category':category_id,'brand':brand_id
    }, 'test.png', png)
    product_id_text = sql("SELECT id FROM product WHERE sku='SMOKE%s'" % tag)
    product_id = int(product_id_text) if product_id_text else None
    check('admin upload and create product', code == 200 and bool(product_id))
    code, _, html = request(site, 'san-pham/smoke-%d.html' % product_id)
    check('new product visible', code == 200 and 'Smoke Product' in html)
    code, _, html = request(admin, 'admin/index.php?c=product&a=update', {
        '_csrf':admin_csrf,'id':product_id,'name':'Smoke Product Edited '+tag,
        'barcode':'SMOKE'+tag,'sku':'SMOKE'+tag,'price':'12000',
        'discount_percentage':'0','discount_from_date':'','discount_to_date':'',
        'inventory_qty':'3','description':'Updated','category':category_id,'brand':brand_id
    })
    check('admin edit product', code == 200 and sql('SELECT price FROM product WHERE id=%d' % product_id) == '12000')
    code, _, html = request(admin, 'admin/index.php?c=product&a=delete&id=%d&_csrf=%s' % (product_id,admin_csrf))
    check('admin delete product', code == 200 and not sql('SELECT id FROM product WHERE id=%d' % product_id))
    product_id = None
finally:
    if article_id: sql('DELETE FROM article WHERE id=%d' % article_id)
    sql("DELETE FROM newsletter WHERE email='%s'" % email)
    if temp_category_id: sql('DELETE FROM category WHERE id=%d' % temp_category_id)
    if temp_brand_id: sql('DELETE FROM brand WHERE id=%d' % temp_brand_id)
    if product_id:
        file_name = sql('SELECT featured_image FROM product WHERE id=%d' % product_id)
        sql('DELETE FROM product WHERE id=%d' % product_id)
        if file_name: (pathlib.Path(__file__).resolve().parents[1]/'upload'/file_name).unlink(missing_ok=True)
    if admin_order_id:
        current_status = sql('SELECT order_status_id FROM `order` WHERE id=%d' % admin_order_id)
        if current_status and current_status != '6':
            sql('UPDATE product p JOIN order_item i ON i.product_id=p.id SET p.inventory_qty=p.inventory_qty+i.qty WHERE i.order_id=%d' % admin_order_id)
        sql('DELETE FROM order_item WHERE order_id=%d' % admin_order_id)
        sql('DELETE FROM `order` WHERE id=%d' % admin_order_id)
    if order_id:
        sql('UPDATE product p JOIN order_item i ON i.product_id=p.id SET p.inventory_qty=p.inventory_qty+i.qty WHERE i.order_id=%d' % order_id)
        sql('DELETE FROM order_item WHERE order_id=%d' % order_id)
        sql('DELETE FROM `order` WHERE id=%d' % order_id)
    if staff_id: sql('DELETE FROM staff WHERE id=%d' % staff_id)
    if customer_id: sql('DELETE FROM customer WHERE id=%d' % customer_id)
