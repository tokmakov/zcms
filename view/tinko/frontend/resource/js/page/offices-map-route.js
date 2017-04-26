var routeAlreadyAdded = false;
var tinkoMap, tinkoRoute, markerNum = 1;
var routeMarkers = [];
var routePoints = [];
$(document).ready(function() {
    $('#remove-route-button')
        .css({'cursor' : 'pointer', 'color' : '#e9751f', 'border-bottom' : '1px dashed #e9751f'})
        .hide()
        .click(removeRoute);
});

// Как только будет загружен API и готов DOM, выполняем инициализацию
ymaps.ready(initOfficesMap);
function initOfficesMap() {
    tinkoMap = new ymaps.Map('tinkoMap', { center: [55.750, 37.607], zoom: 11 });
    // tinkoMap.behaviors.enable('scrollZoom');
    // Добавляем элементы управления
    tinkoMap.controls.add('zoomControl').add('typeSelector').add('mapTools');

    // Создаем метку центрального офиса
    var centralOfficePlacemark = new ymaps.Placemark([55.752422, 37.77163], {
        // Свойства
        iconContent: 'Центральный',
        name: 'Центральный офис',
        address: '3-й проезд Перова поля, дом 8',
        phone: 'тел: (495) 708-42-13, факc: (495) 708-42-14',
        photo: '<img src="/files/page/41/office.jpg" alt="Центральный офис" />',
        print: '<a href="/files/page/41/print.jpg" target="_blank" />Версия для печати</a>'
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    centralOfficePlacemark.events.add('click', function (e) {
        if ( routeMarkers.length > 0 && !routeAlreadyAdded ) {
            e.preventDefault();
            calcRoute(1);
        }
    });
    // Создаем метку офиса продаж Сокол
    var sokolOfficePlacemark = new ymaps.Placemark([55.810463, 37.524699], {
        // Свойства
        iconContent: 'Сокол',
        name: 'Офис продаж Сокол',
        address: 'ул. Часовая, д. 24, стр. 2',
        phone: 'тел: (495) 708-42-13 доб. 401',
        photo: '<img src="/files/page/41/office1.jpg" alt="Офис продаж Сокол" />',
        print: '<a href="/files/page/41/print1.jpg" target="_blank" />Версия для печати</a>'
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    sokolOfficePlacemark.events.add('click', function (e) {
        if ( routeMarkers.length > 0 && !routeAlreadyAdded ) {
            e.preventDefault();
            calcRoute(2);
        }
    });
    // Создаем метку офиса продаж Мещанский
    var olimpOfficePlacemark = new ymaps.Placemark([55.781294, 37.629261], {
        // Свойства
        iconContent: 'Мещанский',
        name: 'Офис продаж Мещанский',
        address: 'ул. Щепкина, д. 47',
        phone: 'тел: (495) 708-42-13 доб. 402',
        photo: '<img src="/files/page/41/office2.jpg" alt="Офис продаж Мещанский" />',
        print: '<a href="/files/page/41/print2.jpg" target="_blank" />Версия для печати</a>'
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    olimpOfficePlacemark.events.add('click', function (e) {
        if ( routeMarkers.length > 0 && !routeAlreadyAdded ) {
            e.preventDefault();
            calcRoute(3);
        }
    });
    // Создаем метку офиса продаж Нагорный
    var nagorOfficePlacemark = new ymaps.Placemark([55.678815, 37.603392], {
        // Свойства
        iconContent: 'Нагорный',
        name: 'Офис продаж Нагорный',
        address: 'ул. Нагорная, д. 20, корп. 1',
        phone: 'тел: (495) 708-42-13 доб. 403',
        photo: '<img src="/files/page/41/office3.gif" alt="Офис продаж Нагорный" />',
        print: '<a href="/files/page/41/print3.jpg" target="_blank" />Версия для печати</a>'
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    nagorOfficePlacemark.events.add('click', function (e) {
        if ( routeMarkers.length > 0 && !routeAlreadyAdded ) {
            e.preventDefault();
            calcRoute(4);
        }
    });

    // Создаем коллекцию, в которую будем добавлять метки
    tinkoCollection = new ymaps.GeoObjectCollection();

    //Добавляем метки в коллекцию геообъектов.
    tinkoCollection
        .add(centralOfficePlacemark)
        .add(sokolOfficePlacemark)
        .add(olimpOfficePlacemark)
        .add(nagorOfficePlacemark);

    // Создаем шаблон для отображения контента балуна
    var tinkoBalloonLayout = ymaps.templateLayoutFactory.createClass(
        '<div><strong>$[properties.name]</strong></div>' +
        '<div>$[properties.address]</div>' +
        '<div>$[properties.phone]</div>' +
        '<div>$[properties.photo]</div>' +
        '<div>$[properties.print]</div>'
    );

    // Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'tinko#officeslayout'.
    ymaps.layout.storage.add('tinko#officeslayout', tinkoBalloonLayout);

    // Задаем наш шаблон для балунов геобъектов коллекции
    tinkoCollection.options.set({
        balloonContentBodyLayout:'tinko#officeslayout',
        // Максимальная ширина балуна в пикселах
        balloonMaxWidth: 400
    });

    // Добавляем коллекцию геообъектов на карту.
    tinkoMap.geoObjects.add(tinkoCollection);

    //Отслеживаем событие клика по карте
    tinkoMap.events.add('click', function (e) {
        var coords = e.get('coordPosition');
        if (routeMarkers.length < 9) {
            if ( routeAlreadyAdded ) removeRoute();
            myPlacemark = new ymaps.Placemark([coords[0].toPrecision(6),coords[1].toPrecision(6)], {
                // Свойства
                // Текст метки
                iconContent: markerNum
            }, {
                // Опции
                // Иконка метки будет растягиваться под ее контент
                preset: 'twirl#blueStretchyIcon'
            });
            routeMarkers.push(myPlacemark);
            tinkoMap.geoObjects.add(myPlacemark);
            markerNum++;
        } else {
            alert('Вы задали максимальное количество точек');
        }
    });

}

function calcRoute(office) {
    for (var i = 0, l = routeMarkers.length; i < l; i++) {
        routePoints[i] = routeMarkers[i].geometry.getCoordinates();
    }
    // Добавляем конечную точку маршрута
    switch ( office ) {
        case 1: routePoints[i] = [55.752422, 37.77163]; break;  // Центральный офис
        case 2: routePoints[i] = [55.810463, 37.524699]; break; // офис продаж Сокол
        case 3: routePoints[i] = [55.781294, 37.629261]; break; // офис продаж Мещанский
        case 4: routePoints[i] = [55.678815, 37.603392]; break; // офис продаж Нагорный
    }
    ymaps.route(routePoints, {
        // Опции маршрутизатора
        mapStateAutoApply: true // автоматически позиционировать карту
    }).then(function (router) {
        tinkoRoute = router;
        tinkoMap.geoObjects.add(tinkoRoute);
        var distance = tinkoRoute.getLength()*0.001;
        $('#routeLength').html('Длина маршрута ' + distance.toFixed(2) + ' км.');
        $('#remove-route-button').show();
        routeAlreadyAdded = true;
    }, function (error) {
        alert('Возникла ошибка: ' + error.message);
    });
}

// Удаление маршрута и меток
function removeRoute() {
    tinkoRoute && tinkoMap.geoObjects.remove(tinkoRoute);
    for (var i = 0, l = routeMarkers.length; i < l; i++) {
        tinkoMap.geoObjects.remove(routeMarkers[i]);
    }
    routeMarkers = [];
    routePoints = [];
    markerNum = 1;
    routeAlreadyAdded = false;
    $('#routeLength').empty();
    $('#remove-route-button').hide();
}