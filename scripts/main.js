import '../styles/main.scss'

import UIkit from 'uikit'
import UIkitIcons from 'uikit/dist/js/uikit-icons'


document.addEventListener('DOMContentLoaded', function() {
	const util = UIkit.util
    UIkit.use(UIkitIcons)

    var formatter = new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
				minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

		if (document.getElementById('minusqnt')) {
			document.getElementById('minusqnt').onclick = function(event) {
					event.preventDefault()
					const qnt = document.getElementById('qnt')
					if (Number(qnt.value) > 1) {
							qnt.value = parseInt(qnt.value) - 1
					}
			}
		}

		if (document.getElementById('plusqnt')) {
			document.getElementById('plusqnt').onclick = function(event) {
					event.preventDefault()
					const qnt = document.getElementById('qnt')
					qnt.value = parseInt(qnt.value) + 1
			}
		}

    util.on(util.$('#addtocart'), 'click', function(event) {
        var c = false
        util.$$('input[name="size"]').forEach(e => {
            if (e.checked) {
                c = true
            }
        })
        if (!c) {
            event.preventDefault()
            if (!util.isVisible(util.$('#error'))) {
                UIkit.toggle('#error').toggle()
            }
        }
    })

    util.$$('input[name="size"]').forEach(element => {
        util.on(element, 'click', function() {
            if (util.isVisible(util.$('#error'))) {
                UIkit.toggle('#error').toggle()
            }
            util.ajax('/api/sizedescription/?id=' + element.value, { responseType: 'json' })
                .then(function(xhr) {
                    var message = util.$('#size-description')
                    message.innerHTML = xhr.response.data.description
                    if (!util.isVisible(message)) {
                        UIkit.toggle(message).toggle()
                    }
                    util.animate(message, 'uk-animation-fade', 200)
                });
        })
    });

    function recount(id, addone = true) {
        var qnt = util.$('#qnt-' + id);
        var size = util.$('#size-' + id).getAttribute('data-size');
        var price = util.$('#price-' + id);
        var sum = util.$('#sum-' + id);
        if (addone) {
            qnt.value++;
        } else {
            if (qnt.value <= 1) {
                return
            }
            qnt.value--;
        }
        var productSum = parseInt(qnt.value) * parseInt(price.getAttribute('data-price'));
        sum.innerHTML = formatter.format(productSum);
        sum.setAttribute('data-sum', productSum)

        var total = 0;
        util.$$('.productsum').forEach(element => {
            total = total + parseInt(element.getAttribute('data-sum'))
        });
        var tqnt = 0;
        util.$$('input[name="qnt"]').forEach(element => {
            tqnt = tqnt + parseInt(element.value)
        });
        util.$('#tqnt').innerHTML = tqnt;
        util.$('#total').innerHTML = formatter.format(total)
        util.$('#vsego').innerHTML = formatter.format(total)


        util.ajax('/api/changecart/?id=' + id + '&size=' + size + '&add=' + addone, { responseType: 'json' })
    }

    util.$$('button[name="plus"]').forEach(element => {
        util.on(element, 'click', function() {
          recount(element.value, true)
        })
    });

    util.$$('button[name="minus"]').forEach(element => {
        util.on(element, 'click', function() {
            recount(element.value, false)
        })
    });

    if (document.getElementById('forpvzsdek')) {
        let location = [37.453937, 55.682113];
        if ("geolocation" in navigator){ 
            console.log ("Geolocation available!");
            navigator.geolocation.getCurrentPosition(function(position){ 
                if (position.coords.longitude) {
                    lon = position.coords.longitude;
                } else {
                    lon = 37.453937;
                }
                if (position.coords.latitude) {
                    lat = position.coords.latitude;
                } else {
                    lat = 55.682113;
                }
                document.cookie = "lon=" + lon;
                document.cookie = "lat=" + lat;
            });
            let resultslon = document.cookie.match(/lon=(.+?)(;|$)/);
            let lon = 37.453937;
            if (resultslon) {
                lon = resultslon[1];
            } 
            let resultslat = document.cookie.match(/lat=(.+?)(;|$)/);
            let lat = 55.682113;
            if (resultslat) {
                lat = resultslat[1];
            }
            location = [lon, lat]
        } else {
            console.log ("Browser doesn't support geolocation!");
        }

        console.log (location);

        new window.CDEKWidget({ 
            from: 'Москва',
            root: 'forpvzsdek',
            apiKey: 'ed0e0cc1-c7b8-4435-bbd5-2b4a55430a27',
            canChoose: true,
            servicePath: '/sdek/',
            hideFilters: {
                have_cashless: false,
                have_cash: false,
                is_dressing_room: false,
                type: false,
            },
            forceFilters: {
                type: 'PVZ',
            },
            hideDeliveryOptions: {
                office: false,
                door: false,
            },
            debug: false,
            goods: window.goods,
            defaultLocation: location,
            lang: 'rus',
            currency: 'RUB',
            tariffs: {
            office: [136],
            door: [137],
            // office: [234, 136, 138],
            // door: [233, 137, 139],
            },
            onReady() {},
            onCalculate() {},
            onChoose(type, tariff, address) {
                onChoose(type, tariff, address);
            },
        });

        function updateCart(deliveryPrice, deliveryType, deliveryAddress) {
            util.$('#deliveryAdress').innerHTML = 'Адрес: ' + deliveryAddress
            util.$('#deliveryType').innerHTML = 'Способ доставки: ' + deliveryType
            util.$('#deliveryPrice').innerHTML = formatter.format(deliveryPrice)
            util.$('input[name="address"]').value = deliveryAddress
            util.$('textarea[name="delivery_type"]').value = deliveryType
            util.$('input[name="delivery_price"]').value = deliveryPrice
            let sum = util.$('#cart_price').innerHTML.replace(/[^0-9]/g, '');
            util.$('#total').innerHTML = formatter.format(parseInt(deliveryPrice) + parseInt(sum))
            util.animate(util.$('#total'), 'uk-animation-scale-down', 200, '', false)
        }

        function onChoose(type, tariff, address) {
            if (type === 'door') {
                //console.log('Выбрана доставка курьером по адресу ' + address.formatted +  ' Цена ' + tariff.delivery_sum + ' Срок ' + tariff.period_max + ' дн.');
                var deliveryPrice = tariff.delivery_sum;
                var deliveryType = 'Доставка курьером по адресу.';
                var deliveryAddress = address.formatted;
                updateCart(deliveryPrice, deliveryType, deliveryAddress);
                UIkit.modal('#modal-sdek').hide();
            } else {
                //console.log('Выбран пункт выдачи заказа ' + address.address + ' Цена ' + tariff.delivery_sum + ' Срок ' + tariff.period_max + ' дн.');
                var deliveryPrice = tariff.delivery_sum;
                var deliveryType = 'Доставка в пункт выдачи - (' + address.name + ')<br>Время работы: ' + address.work_time + '.';
                var deliveryAddress = address.city + ', ' + address.address;
                updateCart(deliveryPrice, deliveryType, deliveryAddress);
                UIkit.modal('#modal-sdek').hide();
            }
        }

        util.$$('input[name="delivery"]').forEach(element => {
            util.on(element, 'click', function() {
                const address = util.$('.toggle-address')
                if (element.checked && element.id != 'pickup') {
                    address.hidden = false
					UIkit.modal('#modal-sdek').show();
                } else {
                    address.hidden = true
                }
                updateCart(0, 'не указан', 'не выбран')
            })
        });

        util.on(util.$('#forpvz'), 'click', function(event) {
            event.preventDefault()
        })
    }

    document.querySelector('#confirm').onchange = function() {  
        if (this.checked) {
            document.getElementById('dtn_order').disabled = false;
        } else {
            document.getElementById('dtn_order').disabled = true;
        }
    }; 
})

//Скрипты всплывашки cookies
window.onload = function () {
    if (document.getElementById('cookies')) {
        setTimeout(() => {
            document.getElementById('cookies').classList.remove('hide-cookies');
            document.getElementById('cookies').classList.add('show-cookies');
        }, 2000);
    }
}

document.addEventListener("click", function (e) {
    if (e.target.className == "uk-button uk-button-secondary uk-cookies-link-acept") {
        document.getElementById('cookies').classList.add('hide-cookies');
        document.getElementById('cookies').classList.remove('show-cookies');
        document.cookie = "berkytt_messages_cookies=true; path=/; max-age=31556926";
    }
});

document.addEventListener("click", function (e) {
    if (e.target.className == "uk-cookies-close") {
        document.getElementById('cookies').classList.add('hide-cookies');
        document.getElementById('cookies').classList.remove('show-cookies');
    }
});
//Скрипты всплывашки cookies