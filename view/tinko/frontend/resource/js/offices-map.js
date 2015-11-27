ymaps.ready(initYandexMap);

// Карта, все офисы, инициализация
function initYandexMap () {
    var tinkoMap = new ymaps.Map('tinkoMap', { center: [55.750, 37.607], zoom: 11 });
    // tinkoMap.behaviors.enable('scrollZoom');
    // Добавляем элементы управления
    tinkoMap.controls.add('zoomControl').add('typeSelector').add('mapTools');
    // Добавляем метку центрального офиса
    var centralOfficePlacemark = new ymaps.Placemark([55.752422, 37.77163], {
        // Свойства
        iconContent: 'Центральный',
        balloonContentHeader: 'Центральный офис',
        balloonContentBody: '3-й проезд Перова поля, дом 8'
        /* balloonContentFooter: 'Торговый Дом ТИНКО' */
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    tinkoMap.geoObjects.add(centralOfficePlacemark);
    // Добавляем метку офиса продаж Сокол
    var sokolOfficePlacemark = new ymaps.Placemark([55.810463, 37.524699], {
        // Свойства
        iconContent: 'Сокол',
        balloonContentHeader: 'Офис продаж Сокол',
        balloonContentBody: 'ул. Часовая, д. 24, стр. 2'
        /* balloonContentFooter: 'Торговый Дом ТИНКО' */
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    tinkoMap.geoObjects.add(sokolOfficePlacemark);
    // Добавляем метку офиса продаж Мещанский
    var olimpOfficePlacemark = new ymaps.Placemark([55.781294, 37.629261], {
        // Свойства
        iconContent: 'Мещанский',
        balloonContentHeader: 'Офис продаж Мещанский',
        balloonContentBody: 'ул. Щепкина, д. 47'
        /* balloonContentFooter: 'Торговый Дом ТИНКО' */
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    tinkoMap.geoObjects.add(olimpOfficePlacemark);
    // Добавляем метку офиса продаж Нагорный
    var nagorOfficePlacemark = new ymaps.Placemark([55.678815, 37.603392], {
        // Свойства
        iconContent: 'Нагорный',
        balloonContentHeader: 'Офис продаж Нагорный',
        balloonContentBody: 'ул. Нагорная, д. 20, корп. 1'
        /* balloonContentFooter: 'Торговый Дом ТИНКО' */
    }, {
        // Опции
        preset: 'twirl#redStretchyIcon' // иконка растягивается под контент
    });
    tinkoMap.geoObjects.add(nagorOfficePlacemark);
}