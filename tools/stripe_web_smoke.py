"""Exercise XAMPP -> Stripe test Checkout -> admin cancellation, then clean up."""
import hashlib
import http.cookiejar
import re
import secrets
import subprocess
import urllib.error
import urllib.parse
import urllib.request

BASE='http://localhost/backend/godashop/'
MYSQL='C:/xampp/mysql/bin/mysql.exe'
DB='godashop_recovered_20260919'
tag=secrets.token_hex(7)
email='stripe-smoke-'+tag+'@example.invalid'
username='stripe_'+tag
password='Test_'+secrets.token_urlsafe(16)
customer_id=staff_id=order_id=None
stock_before=None

def sql(query):
    result=subprocess.run([MYSQL,'-u','root','-N','-B',DB,'-e',query],capture_output=True,text=True)
    if result.returncode: raise RuntimeError(result.stderr)
    return result.stdout.strip()

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl): return None

def client():
    jar=http.cookiejar.CookieJar()
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar)), urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar),NoRedirect())

def request(opener,path,data=None):
    body=urllib.parse.urlencode(data).encode() if data is not None else None
    try:
        response=opener.open(urllib.request.Request(BASE+path,body),timeout=35)
        return response.status,response.url,response.read().decode('utf-8','replace'),response.headers
    except urllib.error.HTTPError as error:
        return error.code,error.url,error.read().decode('utf-8','replace'),error.headers

def csrf(html):
    match=re.search(r'name="_csrf" value="([a-f0-9]+)"',html)
    if not match: raise AssertionError('CSRF missing')
    return match.group(1)

try:
    bcrypt=subprocess.run(['C:/xampp/php/php.exe','-r','echo password_hash($argv[1], PASSWORD_DEFAULT);',password],capture_output=True,text=True,check=True).stdout
    sql("INSERT INTO customer (name,password,mobile,email,login_by,shipping_name,shipping_mobile,ward_id,housenumber_street,is_active) VALUES ('Stripe Smoke','%s','0900000000','%s','form','Stripe Smoke','0900000000',NULL,'Test',1)" % (bcrypt,email))
    customer_id=int(sql("SELECT id FROM customer WHERE email='%s'" % email))
    sql("INSERT INTO staff (role_id,name,mobile,username,password,email,is_active) VALUES (1,'Stripe Smoke','0900000000','%s','%s','%s',1)" % (username,hashlib.md5(password.encode()).hexdigest(),email))
    staff_id=int(sql("SELECT id FROM staff WHERE username='%s'" % username))
    stock_before=int(sql('SELECT inventory_qty FROM product WHERE id=2'))

    site,site_no_redirect=client()
    _,_,home,_=request(site,'')
    token=csrf(home)
    status,_,_,_=request(site,'index.php?c=auth&a=login',{'email':email,'password':password,'_csrf':token})
    assert status==200
    status,_,_,_=request(site,'index.php?c=cart&a=add',{'product_id':2,'qty':1,'_csrf':token})
    assert status==200
    status,_,checkout,_=request(site,'index.php?c=payment&a=checkout')
    checkout_token=re.search(r'name="checkout_token" value="([a-f0-9]+)"',checkout).group(1)
    if not (status==200 and 'value="2" disabled' not in checkout):
        position=checkout.find('name="payment_method" value="2"')
        raise AssertionError((status,checkout[position:position+210]))
    status,_,response,headers=request(site_no_redirect,'index.php?c=payment&a=order',{
        '_csrf':token,'checkout_token':checkout_token,'fullname':'Stripe Smoke','mobile':'0900000000',
        'province':'01','district':'001','ward':'00001','address':'Test','payment_method':'2'
    })
    order_text=sql('SELECT id FROM `order` WHERE customer_id=%d ORDER BY id DESC LIMIT 1' % customer_id)
    order_id=int(order_text) if order_text else None
    assert status==303 and headers.get('Location','').startswith('https://checkout.stripe.com/') and order_id, (status,response[:160])
    assert sql('SELECT status FROM stripe_payment WHERE order_id=%d' % order_id)=='pending'
    print('PASS XAMPP created a real Stripe test Checkout URL')

    admin,_=client()
    _,_,admin_login,_=request(admin,'admin/login.php')
    admin_token=csrf(admin_login)
    status,_,_,_=request(admin,'admin/index.php?c=auth&a=login',{'username':username,'password':password,'_csrf':admin_token})
    assert status==200
    status,_,response,_=request(admin,'admin/index.php?c=order&a=update',{'order_id':order_id,'status':'6','staff_id':staff_id,'_csrf':admin_token})
    assert status==200 and sql('SELECT status FROM stripe_payment WHERE order_id=%d' % order_id)=='expired', (status,response[:160])
    assert int(sql('SELECT inventory_qty FROM product WHERE id=2'))==stock_before
    print('PASS admin expired Stripe Checkout and restored stock')
    status,_,response,_=request(admin,'admin/index.php?c=order&a=delete&order_id=%d&_csrf=%s' % (order_id,admin_token))
    assert status==200 and not sql('SELECT id FROM `order` WHERE id=%d' % order_id),(status,response[:160])
    order_id=None
    print('PASS canceled test order removed')
finally:
    if order_id:
        state=sql('SELECT order_status_id FROM `order` WHERE id=%d' % order_id)
        if state and state!='6': sql('UPDATE product p JOIN order_item i ON i.product_id=p.id SET p.inventory_qty=p.inventory_qty+i.qty WHERE i.order_id=%d' % order_id)
        sql('DELETE FROM order_item WHERE order_id=%d' % order_id)
        sql('DELETE FROM `order` WHERE id=%d' % order_id)
    if staff_id: sql('DELETE FROM staff WHERE id=%d' % staff_id)
    if customer_id: sql('DELETE FROM customer WHERE id=%d' % customer_id)
