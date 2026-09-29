function openMenuMobile() {
    $(".menu-mb").width("250px");
    $(".btn-menu-mb").hide("slow");
}

function closeMenuMobile() {
    $(".menu-mb").width(0);
    $(".btn-menu-mb").show("slow");
}




// Toàn bộ thẻ html phải tải rồi thì code $(fucntion(){...}) mới chạy
$(function () {
    const csrfToken = window.CSRF_TOKEN || $('meta[name="csrf-token"]').attr('content') || '';
    $.ajaxSetup({ headers: { 'X-CSRF-Token': csrfToken } });
    $(document).ajaxError(function (event, xhr) {
        // Server errors are logged for debugging; don't expose PHP warnings in a browser alert.
        console.error('Request failed:', xhr.status, xhr.responseText);
        $('.message, .error').stop(true, true).html('Không thể xử lý yêu cầu. Vui lòng thử lại.').show();
    });
    $(".form-reset-password").validate({
        rules: {
            // simple rule, converted to {required:true}
            password: {
                required: true,
                regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
            },

            password_confirmation: {
                required: true,
                equalTo: "[name=password]"
            },
        },

        messages: {
            password: {
                required: "Vui lòng nhập mật khẩu",
                regex: "Mật khẩu ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt",
            },

            password_confirmation: {
                required: "Vui lòng nhập lại mật khẩu",
                equalTo: "Nhập lại mật khẩu phải trùng khớp",
            },
        },

    });


    // form - forgot - password
    $(".form-forgot-password").validate({
        rules: {
            // simple rule, converted to {required:true}
            email: {
                required: true,
                maxlength: 50,
                email: true,
                // server trả về false là lỗi, true là không lỗi
                // remote: "?c=customer&a=notExistingEmail"
            }
        },

        messages: {
            email: {
                required: "Vui lòng nhập email",
                maxlength: "Vui lòng nhập không quá 50 ký tự",
                email: "Vui lòng nhập đúng định dạng email. vd: a@gmail.com",
                // remote: "Email đã được đăng ký. Vui lòng nhập lại."
            }
        },

    });


    updateCart();

    $(".form-register").validate({
        rules: {
            // simple rule, converted to {required:true}
            fullname: {
                required: true,
                maxlength: 50,
                regex:
                    /^[a-zAZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/,
            },

            email: {
                required: true,
                maxlength: 50,
                email: true,
                // server trả về false là lỗi, true là không lỗi
                remote: window.APP_BASE + "/index.php?c=customer&a=notExistingEmail"
            },

            password: {
                required: true,
                regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
            },

            password_confirmation: {
                required: true,
                equalTo: "[name=password]"
            },

        },

        messages: {
            fullname: {
                required: "Vui lòng nhập họ và tên",
                maxlength: "Vui lòng nhập không quá 50 ký tự",
                regex: "Vui lòng nhập số và ký tự đặc biệt",
            },
            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập 10 con số bắt đầu là 0',
            },
            email: {
                required: "Vui lòng nhập email",
                maxlength: "Vui lòng nhập không quá 50 ký tự",
                email: "Vui lòng nhập đúng định dạng email. vd: a@gmail.com",
                remote: "Email đã được đăng ký. Vui lòng nhập lại."
            },
            password: {
                required: "Vui lòng nhập mật khẩu",
                regex: "Mật khẩu ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt",
            },

            password_confirmation: {
                required: "Vui lòng nhập lại mật khẩu",
                equalTo: "Nhập lại mật khẩu phải trùng khớp",
            },
        },

    });

    $('.district').change(function (e) {
        e.preventDefault();

        const district_id = $(this).val();

        $.ajax({
            type: "GET",
            url: window.APP_BASE + "/index.php?c=address&a=getWards",
            data: { district_id: district_id },
            success: function (data) {
                // alert(data);
                // updateSelectBox('.district', data);
                updateSelectBox('.ward', data);
            }
        })
    });

    $('.province').change(function (e) {
        e.preventDefault();

        const province_id = $(this).val();

        $.ajax({
            type: "GET",
            url: window.APP_BASE + "/index.php?c=address&a=getDistricts",
            data: { province_id: province_id },
            success: function (data) {
                // alert(data);
                updateSelectBox('.district', data);
                updateSelectBox('.ward', null);
            }
        })

        if ($('.shipping-fee').length > 0) {
            $.ajax({
                type: "GET",
                url: window.APP_BASE + "/index.php?c=address&a=getShippingFee",
                data: { province_id: province_id },
                success: function (response) {
                    // alert(response)
                    const shipping_fee = response
                    $('.shipping-fee').html(formatMoney(response) + "₫");
                    const sub_total_price = $('.payment-total').attr('data');
                    const total_price = Number(sub_total_price) + Number(shipping_fee);

                    $('.payment-total').html(formatMoney(total_price) + "₫")
                }
            });
        }
    });


    $('main .buy-in-detail').click(function (e) {
        // Ngăn chặn không cho href chạy
        e.preventDefault()
        const product_id = $(this).attr('product-id');
        const qty = $('.product-quantity').val();
        // alert(qty);
        // alert(1)
        // ajax
        $.ajax({
            type: "POST",
            url: window.APP_BASE + "/index.php?c=cart&a=add",
            data: { product_id: product_id, qty: qty },
            success: function (response) {
                updateCart()
            }
        });
        // alert(product_id)
    });

    $('main .buy').click(function (e) {
        // alert(1)
        // Ngăn chặn không cho href chạy
        // e.preventDefault()
        const product_id = $(this).attr('product-id');
        // alert(1)
        // ajax
        $.ajax({
            type: "POST",
            url: window.APP_BASE + "/index.php?c=cart&a=add",
            data: { product_id: product_id, qty: 1 },
            success: function (response) {
                updateCart()
            }
        });

    });

    $(".info-account").validate({
        rules: {
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i
            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/
            },


            password: {
                regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
            },


            password_confirmation: {
                equalTo: '[name=password]'
            }

        },

        messages: {

            fullname: {
                required: 'Vui lòng nhập họ và tên',
                maxlength: 'Vui lòng nhập không quá 20 ký tự',
                regex: 'Vui lòng không nhập số hoặc ký tự đặc biệt'
            },

            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập đúng định dạng số điện thoại. vd: 0932538468'
            },

            password: {
                regex: 'Vui lòng nhập ít nhất 8 ký tự bao gồm ký tự thường, ký tự hoa, số và ký tự đặc biệt'
            },

            password_confirmation: {
                equalTo: 'Mật khẩu không trùng khớp. Vui lòng nhập lại'
            }

        },
    });

    $(".form-login").validate({
        rules: {
            email: {
                required: true,
                maxlength: 50,
                email: true
            },
            password: {
                required: true,
                // email: true

            },

        },

        messages: {
            email: {
                required: 'Vui lòng nhập email',
                email: 'vui lòng nhập đúng định dạng email. vd: avx@gmail.com'
            },
            password: {
                required: 'Vui lòng nhập mật khẩu',
            }

        },
    });


    $(".form-comment").validate({
        rules: {
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i
            },
            email: {
                required: true,
                email: true
            },
            description: {
                required: true,

            },

        },

        messages: {
            fullname: {
                required: 'Vui lòng nhập họ tên',
                maxlength: 'Vui lòng nhập email',
                regex: 'Vui lòng không nhập số hoặc ký tự đặc biệt'
            },
            email: {
                required: 'Vui lòng nhập email',
                email: 'vui lòng nhập đúng định dạng email. vd: avx@gmail.com'
            },
            description: {
                required: 'Vui lòng nhập nội dung',
            },

        },

        // jquery validation hỗ trợ cái anyf
        submitHandler: function (form) {
            // alert()
            $('.message').show();
            $('.message').html('<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gửi mail, vui lòng chờ ...');
            $.ajax({
                type: "POST",
                url: window.APP_BASE + "/index.php?c=product&a=storeComment",
                data: $(form).serialize(),


                success: function (response) {
                    // Đây là chỗ mà database gửi lên sẽ gửi vào chỗ response này 
                    $('.comment-list').html(response);
                    $('.message').hide();

                    // Chuyển value input thành số sao
                    $('main .product-detail .product-description .answered-rating-input').rating({
                        min: 0,
                        max: 5,
                        step: 1,
                        size: 'md',
                        stars: "5",
                        showClear: false,
                        showCaption: false,
                        displayOnly: false,
                        hoverEnabled: true
                    });
                }
            });
        },


    });

    $(".form-contact").validate({
        rules: {
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i
            },
            email: {
                required: true,
                email: true
            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/
            },
            content: {
                required: true,
            },

        },

        messages: {
            fullname: {
                required: 'Vui lòng nhập họ tên',
                maxlength: 'Vui lòng nhập email',
                regex: 'Vui lòng không nhập số hoặc ký tự đặc biệt'
            },
            email: {
                required: 'Vui lòng nhập email',
                email: 'vui lòng nhập đúng định dạng email. vd: avx@gmail.com'
            },
            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập đúng định dạng số điện thoại. vd: 0385548843'
            },
            content: {
                required: 'Vui lòng nhập nội dung',
            },

        },

        // jquery validation hỗ trợ cái anyf
        submitHandler: function (form) {
            // alert()
            $('.message').show();
            $('.message').html('<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gửi mail, vui lòng chờ ...');
            $.ajax({
                type: "POST",
                url: window.APP_BASE + "/index.php?c=contact&a=sendEmail",
                data: $(form).serialize(),


                success: function (response) {
                    // Đây là chỗ mà database gửi lên sẽ gửi vào chỗ response này 
                    $('.message').html(response);
                }
            });
        },


    });


    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );


    // jqChange
    $('#sort-select').change(function (e) {
        const sort = $(this).val();//price-asc
        // url hiện tại là: http://godashop.com/site/?c=product&category_id=2
        // mong muốn là http://godashop.com/site/?c=product&category_id=2&sort=price-asc
        const newURL = getUpdatedParam('sort', sort);
        window.location.href = newURL;

    });

    // jqclick
    $('main .price-range input').click(function (e) {
        const priceRange = $(this).val();//200000-300000
        // header('location: https://vnexpress.net');
        // hiện tại link url là: http://godashop.com/site/?c=product&category_id=4
        // => http://godashop.com/site/?c=product&price-range=200000-300000
        // window.location.href = `?c=product&price-range=${priceRange}`;
        const newURL = getUpdatedParam('price-range', priceRange);
        window.location.href = newURL;

    });

    $(".product-container").hover(function () {
        $(this).children(".button-product-action").toggle(400);
    });

    // Display or hidden button back to top
    $(window).scroll(function () {
        if ($(this).scrollTop()) {
            $(".back-to-top").fadeIn();
        }
        else {
            $(".back-to-top").fadeOut();
        }
    });

    // Khi click vào button back to top, sẽ cuộn lên đầu trang web trong vòng 0.8s
    $(".back-to-top").click(function () {
        $("html").animate({ scrollTop: 0 }, 800);
    });

    // Hiển thị form đăng ký
    $('.btn-register').click(function () {
        $('#modal-login').modal('hide');
        $('#modal-register').modal('show');
    });

    // Hiển thị form forgot password
    $('.btn-forgot-password').click(function () {
        $('#modal-login').modal('hide');
        $('#modal-forgot-password').modal('show');
    });

    // Hiển thị form đăng nhập
    $('.btn-login').click(function () {
        $('#modal-login').modal('show');
    });

    // Fix add padding-right 17px to body after close modal
    // Don't rememeber also attach with fix css
    $('.modal').on('hide.bs.modal', function (e) {
        e.stopPropagation();
        $("body").css("padding-right", 0);

    });

    // Hiển thị cart dialog
    $('.btn-cart-detail').click(function () {
        $('#modal-cart-detail').modal('show');
    });

    // Hiển thị aside menu mobile
    $('.btn-aside-mobile').click(function () {
        $("main aside .inner-aside").toggle();
    });

    // Hiển thị carousel for product thumnail
    $('main .product-detail .product-detail-carousel-slider .owl-carousel').owlCarousel({
        margin: 10,
        nav: true

    });
    // Bị lỗi hover ở bộ lọc (mobile) & tạo thanh cuộn ngang
    // Khởi tạo zoom khi di chuyển chuột lên hình ở trang chi tiết
    // $('main .product-detail .main-image-thumbnail').ezPlus({
    //     zoomType: 'inner',
    //     cursor: 'crosshair',
    //     responsive: true
    // });

    // Cập nhật hình chính khi click vào thumbnail hình ở slider
    $('main .product-detail .product-detail-carousel-slider img').click(function (event) {
        /* Act on the event */
        $('main .product-detail .main-image-thumbnail').attr("src", $(this).attr("src"));
        var image_path = $('main .product-detail .main-image-thumbnail').attr("src");
        $(".zoomWindow").css("background-image", "url('" + image_path + "')");

    });

    $('main .product-detail .product-description .rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false
    });

    $('main .product-detail .product-description .answered-rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false,
        displayOnly: false,
        hoverEnabled: true
    });

    $('main .ship-checkout[name=payment_method]').click(function (event) {
        /* Act on the event */
    });

    $('input[name=checkout]').click(function (event) {
        /* Act on the event */
        window.location.href = window.APP_BASE + "/index.php?c=payment&a=checkout";
    });

    $('input[name=back-shopping]').click(function (event) {
        /* Act on the event */
        window.location.href = window.APP_BASE + "/san-pham.html";
    });

    // Hiển thị carousel for relative products
    $('main .product-detail .product-related .owl-carousel').owlCarousel({
        // loop: true,
        margin: 10,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }

    });




});

// cập nhật hoặc thếm mới param vào url hiện tại
// với key là sort, val là price-asc
// hiện tại: http://godashop.com/site/?c=product&category_id=2
// trả về url mới là: http://godashop.com/site/?c=product&category_id=2&sort=price-asc
function getUpdatedParam(key, val) {
    // http://godashop.com/site/?c=product&category_id=2
    const currentURL = window.location.href;

    //update param cho url
    const objURL = new URL(currentURL);
    objURL.searchParams.set(key, val);
    if (key !== 'page') objURL.searchParams.delete('page');

    // http://godashop.com/site/?c=product&category_id=2&sort=price-asc
    return objURL.toString();
}






// hiện tại: http://godashop.com/site/?c=product&category_id=2
// với $page có giá trị là 3
// http://godashop.com/site/?c=product&category_id=2&page=3
function goToPage($page) {
    const newURL = getUpdatedParam('page', $page);
    window.location.href = newURL;
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function updateCart() {
    $.getJSON(window.APP_BASE + '/index.php?c=cart&a=index', renderCart);
}

function escapeHtml(value) {
    return $('<span>').text(String(value)).html().replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function renderCart(cart) {
    // console.log(cart);
    $('.number-total-product').html(cart.total_product_number);
    $('.price-total').html(formatMoney(cart.total_price) + 'đ');
    const items = cart.items;
    let rows = '';
    for (const product_id in cart.items) {
        const item = items[product_id]
        const row = `
            <hr>
            <div class="clearfix text-left">
                <div class="row">
                    <div class="col-sm-6 col-md-1">
                        <div><img class="img-responsive" src="${window.APP_BASE}/upload/${encodeURIComponent(item.img)}" alt="${escapeHtml(item.name)}"></div>
                    </div>
                    <div class="col-sm-6 col-md-3"><a class="product-name" href="${escapeHtml(item.url)}">${escapeHtml(item.name)}</a></div>
                    <div class="col-sm-6 col-md-2"><span class="product-item-discount">${formatMoney(item.unit_price)}₫</span></div>
                    <div class="col-sm-6 col-md-3"><input type="hidden" value="1"><input type="number" onchange="updateProductInCart(this,${item.product_id})" min="1" value="${item.qty}"></div>
                    <div class="col-sm-6 col-md-2"><span>${formatMoney(item.total_price)}₫</span></div>
                    <div class="col-sm-6 col-md-1"><a class="remove-product" href="javascript:void(0)" onclick="deleteProductInCart(${item.product_id})"><span class="glyphicon glyphicon-trash"></span></a></div>
                </div>
            </div>
        `;
        rows += row
    }

    $('.cart-product').html(rows);
}

function formatMoney(money) {
    // tham số thứ 4
    return number_format(money, 0, ',', '.');
}

function deleteProductInCart(product_id) {
    // alert(1)
    $.ajax({
        type: "POST",
        url: window.APP_BASE + "/index.php?c=cart&a=delete",
        data: { product_id: product_id },
        success: function (response) {
            updateCart()
        }
    });
}

function updateProductInCart(input, product_id) {
    const qty = $(input).val();
    // alert(qty);
    $.ajax({
        type: "POST",
        url: window.APP_BASE + "/index.php?c=cart&a=update",
        data: { product_id: product_id, qty: qty },
        success: function (response) {
            updateCart()
        }
    });
}

function updateSelectBox(selector, data) {
    const districts = typeof data === "string" ? JSON.parse(data || "[]") : (data || []);
    $(selector).find('option').not(':first').remove();
    if (!data) return;

    for (const district of districts) {
        const option = `<option value='${district.id}'>${district.name}</option>`;
        $(selector).append(option);
    }
}
