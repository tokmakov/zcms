<?php
$routing = array( // поддержка ЧПУ (SEF) для общедоступной части сайта
    'enable'  => true,
    'cap2sef' => array( // Controller/Action/Params => Search Engines Friendly
        /*
         * главная страница сайта
         */
        '~^frontend/index/index$~i' =>
        '',

        /*
         * каталог
         */
        // главная страница каталога
        '~^frontend/catalog/index$~i' =>
        'catalog',
        // категория каталога
        '~^frontend/catalog/category/id/(\d+)$~i' =>
        'catalog/category/$1',
        // 1. категория каталога, фильтр по функционалу
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)$~i' =>
        'catalog/category/$1/group/$2',
        // 2. категория каталога, фильтр по производителю
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)$~i' =>
        'catalog/category/$1/maker/$2',
        // 3. категория каталога, лидеры продаж
        '~^frontend/catalog/category/id/(\d+)/hit/1$~i' =>
        'catalog/category/$1/hit/1',
        // 4. категория каталога, новинки
        '~^frontend/catalog/category/id/(\d+)/new/1$~i' =>
        'catalog/category/$1/new/1',
        // 5. категория каталога, сортировка
        '~^frontend/catalog/category/id/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/sort/$2',
        // 6. категория каталога, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/page/$2',
        // 7. категория каталога, фильтр по функционалу, фильтр по производителю
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3',
        // 8. категория каталога, фильтр по функционалу, лидеры продаж
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1$~i' =>
        'catalog/category/$1/group/$2/hit/1',
        // 9. категория каталога, фильтр по функционалу, новинки
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1$~i' =>
        'catalog/category/$1/group/$2/new/1',
        // 10. категория каталога, фильтр по функционалу, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/param/$3',
        // 11. категория каталога, фильтр по функционалу, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/sort/$3',
        // 12. категория каталога, фильтр по функционалу, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/page/$3',
        // 13. категория каталога, фильтр по производителю, лидеры продаж
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1$~i' =>
        'catalog/category/$1/maker/$2/hit/1',
        // 14. категория каталога, фильтр по производителю, новинки
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/new/1$~i' =>
        'catalog/category/$1/maker/$2/new/1',
        // 15. категория каталога, фильтр по производителю, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/sort/$3',
        // 16. категория каталога, фильтр по производителю, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/page/$3',
        // 17. категория каталога, лидеры продаж, новинки
        '~^frontend/catalog/category/id/(\d+)/hit/1/new/1$~i' =>
        'catalog/category/$1/hit/1/new/1',
        // 18. категория каталога, лидеры продаж, сортировка
        '~^frontend/catalog/category/id/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/category/$1/hit/1/sort/$2',
        // 19. категория каталога, лидеры продаж, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/category/$1/hit/1/page/$2',
        // 20. категория каталога, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/new/1/sort/$2',
        // 21. категория каталога, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/new/1/page/$2',
        // 22. категория каталога, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/sort/$2/page/$3',
        // 23. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1',
        // 24. категория каталога, фильтр по функционалу, фильтр по производителю, новинки
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1',
        // 25. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/param/$4',
        // 26. категория каталога, фильтр по функционалу, фильтр по производителю, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/sort/$4',
        // 27. категория каталога, фильтр по функционалу, фильтр по производителю, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/page/$4',
        // 28. категория каталога, фильтр по функционалу, лидеры продаж, новинки
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1',
        // 29. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/hit/1/param/$3',
        // 30. категория каталога, фильтр по функционалу, лидеры продаж, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/hit/1/sort/$3',
        // 31. категория каталога, фильтр по функционалу, лидеры продаж, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/page/$3',
        // 32. категория каталога, фильтр по функционалу, новинки, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/new/1/param/$3',
        // 33. категория каталога, фильтр по функционалу, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/new/1/sort/$3',
        // 34. категория каталога, фильтр по функционалу, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/new/1/page/$3',
        // 35. категория каталога, фильтр по функционалу, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/param/$3/sort/$4',
        // 36. категория каталога, фильтр по функционалу, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/param/$3/page/$4',
        // 37. категория каталога, фильтр по функционалу, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/sort/$3/page/$4',
        // 38. категория каталога, фильтр по производителю, лидеры продаж, новинки
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1',
        // 39. категория каталога, фильтр по производителю, лидеры продаж, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/sort/$3',
        // 40. категория каталога, фильтр по производителю, лидеры продаж, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/page/$3',
        // 41. категория каталога, фильтр по производителю, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/new/1/sort/$3',
        // 42. категория каталога, фильтр по производителю, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/new/1/page/$3',
        // 43. категория каталога, фильтр по производителю, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/sort/$3/page/$4',
        // 44. категория каталога, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/hit/1/new/1/sort/$2',
        // 45. категория каталога, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/hit/1/new/1/page/$2',
        // 46. категория каталога, лидеры продаж, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/hit/1/sort/$2/page/$3',
        // 47. категория каталога, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/new/1/sort/$2/page/$3',
        // 48. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1',
        // 49. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4',
        // 50. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/sort/$4',
        // 51. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/page/$4',
        // 52. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4',
        // 53. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/sort/$4',
        // 54. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/page/$4',
        // 55. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/param/$4/sort/$5',
        // 56. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/param/$4/page/$5',
        // 57. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/sort/$3',
        // 58. категория каталога, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/page/$3',
        // 59. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4',
        // 60. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/sort/$4',
        // 61. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/page/$4',
        // 62. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/page/$5',
        // 63. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5',
        // 64. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4/page/$5',
        // 65. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/sort/$4/page/$5',
        // 66. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/sort/$3/page/$4',
        // 67. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5',
        // 68. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/page/$5',
        // 69. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/sort/$4/page/$5',
        // 70. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5/page/$6',
        // 71. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4/sort/$5/page/$6',
        // 72. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$5',
        // 73. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5/page/$6',

        // страница товара каталога
        '~^frontend/catalog/product/id/(\d+)$~i' =>
        'catalog/product/$1',
        // страница со списком производителей
        '~^frontend/catalog/allmkrs$~i' =>
        'catalog/all-makers',
        // страница со списком товаров выбранного производителя
        '~^frontend/catalog/maker/id/(\d+)$~i' =>
        'catalog/maker/$1',
        // страница со списком товаров выбранного производителя, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/page/(\d+)$~i' =>
        'catalog/maker/$1/page/$2',
        // страница со списком товаров выбранного производителя, сортировка
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)$~i' =>
        'catalog/maker/$1/sort/$2',
        // страница со списком товаров выбранного производителя, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/sort/$2/page/$3',
        // страница поиска по каталогу
        '~^frontend/catalog/search$~i'=>
        'catalog/search',
        // страница результатов поиска по каталогу
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'catalog/search/query/$1/page/$2',

        // поиск по каталогу, XmlHttpRequest
        '~^frontend/catalog/ajaxsearch$~i' => 'catalog/ajax-search',

        // фильтр для выбранной категории, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)$~i' =>
        'catalog/ajax-filter/category/$1',
        // фильтр для выбранной категории, сортировка, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)/sort/(\d)$~i' =>
        'catalog/ajax-filter/category/$1/sort/$2',
        // фильтр для выбранной категории, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)/group/(\d+)$~i' =>
        'catalog/ajax-filter/category/$1/group/$2',
        // фильтр для выбранной категории, сортировка, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'catalog/ajax-filter/category/$1/group/$2/sort/$3',
        // фильтр для выбранной категории, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)/maker/(\d+)$~i' =>
        'catalog/ajax-filter/category/$1/maker/$2',
        // фильтр для выбранной категории, сортировка, XmlHttpRequest
        '~^frontend/catalog/ajaxfilter/category/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'catalog/ajax-filter/category/$1/maker/$2/sort/$3',

        /*
         * просмотренные товары
         */
        // страница со списком всех просмотренных товаров
        '~^frontend/viewed/index$~i' =>
        'viewed',
        // просмотренные товары, постраничная навигация
        '~^frontend/viewed/index/page/(\d+)$~i' =>
        'viewed/page/$1',

        /*
         * отложенные товары
         */
        // страница со списком всех отложенных товаров
        '~^frontend/wished/index$~i' =>
        'wished',
        // отложенные товары, постраничная навигация
        '~^frontend/wished/index/page/(\d+)$~i' =>
        'wished/page/$1',
        // добавить товар в список отложенных
        '~^frontend/wished/addprd$~i' =>
        'wished/addprd',
        // удалить товар из списка отложенных
        '~^frontend/wished/rmvprd$~i' =>
        'wished/rmvprd',
        // добавить товар в список отложенных, XmlHttpRequest
        '~^frontend/wished/ajax/action/addprd$~i' =>
        'wished/ajax/addprd/',
        // удалить товар из списка отложенных, XmlHttpRequest
        '~^frontend/wished/ajax/action/rmvprd$~i' =>
        'wished/ajax/rmvprd',
        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^frontend/compared/index$~i' =>
        'compared',
        // товары для сравнения, постраничная навигация
        '~^frontend/compared/index/page/(\d+)$~i' =>
        'compared/page/$1',
        // добавить товар в список сравнения
        '~^frontend/compared/addprd$~i' =>
        'compared/addprd',
        // удалить товар из списка сравнения
        '~^frontend/compared/rmvprd$~i' =>
        'compared/rmvprd',
        // добавить товар в список сравнения, XmlHttpRequest
        '~^frontend/compared/ajax/action/addprd$~i' =>
        'compared/ajax/addprd',
        // удалить товар из списка сравнения, XmlHttpRequest
        '~^frontend/compared/ajax/action/rmvprd$~i' =>
        'compared/ajax/rmvprd',

        /*
         * корзина
         */
        // страница покупательской корзины
        '~^frontend/basket/index$~i' =>
        'basket',
        // добавить товар в корзину
        '~^frontend/basket/addprd$~i' =>
        'basket/addprd',
        // удалить товар из корзины
        '~^frontend/basket/rmvprd/id/(\d+)$~i' =>
        'basket/rmvprd/$1',
        // оформление заказ
        '~^frontend/basket/checkout$~i' =>
        'basket/checkout',
        // добавить товар в корзину, XmlHttpRequest
        '~^frontend/basket/ajax/action/addprd$~i' =>
        'basket/ajax/addprd',

        /*
         * пользователи
         */
        // личный кабинет пользователя
        '~^frontend/user/index$~i' =>
        'user/index',
        // страница авторизации пользователя
        '~^frontend/user/login$~i' =>
        'user/login',
        // выйти из личного кабинета
        '~^frontend/user/logout$~i' =>
        'user/logout',
        // страница регистрации нового пользователя
        '~^frontend/user/reg$~i' =>
        'user/reg',
        // страница редактирования личных данных
        '~^frontend/user/edit$~i' =>
        'user/edit',
        // страница со списком профилей
        '~^frontend/user/allprof$~i' =>
        'user/allprof',
        // страница с формой для добавления профиля
        '~^frontend/user/addprof$~i' =>
        'user/addprof',
        // страница с формой для редактирования профиля
        '~^frontend/user/editprof/id/(\d+)$~i' =>
        'user/editprof/$1',
        // удаление профиля
        '~^frontend/user/rmvprof/id/(\d+)$~i' =>
        'user/rmvprof/$1',
        // восстановление пароля
        '~^frontend/user/forgot$~i' =>
        'user/forgot',
        // история заказов
        '~^frontend/user/allorders$~i' => 'user/all-orders',
        // история заказов, постраничная навигация
        '~^frontend/user/allorders/page/(\d+)$~i' =>
        'user/all-orders/page/$1',
        // подробная информация о заказе
        '~^frontend/user/order/id/(\d+)$~i' =>
        'user/order/$1',
        // повторить заказ
        '~^frontend/user/repeat/id/(\d+)$~i' =>
        'user/repeat/$1',
        // получение профиля, XmlHttpRequest
        '~^frontend/user/ajax/id/(\d+)$~i' =>
        'user/ajax/profile/$1',

        /*
         * новости
         */
        // главная страница новостей
        '~^frontend/news/index$~i' =>
        'news',
        // главная страница новостей, постраничная навигация
        '~^frontend/news/index/page/(\d+)$~i' =>
        'news/page/$1',
        // отдельная новость
        '~^frontend/news/item/id/(\d+)$~i' =>
        'news/item/$1',
        // список новостей выбранной категории
        '~^frontend/news/category/id/(\d+)$~i' =>
        'news/ctg/$1',
        // список новостей выбранной категории, постраничная навигация
        '~^frontend/news/category/id/(\d+)/page/(\d+)$~i' =>
        'news/ctg/$1/page/$2',

        /*
         * страницы сайта
         */
        '~^frontend/page/index/id/(\d+)$~i' =>
        'page/$1',

        /*
         * карта сайта
         */
        '~^frontend/sitemap/index$~i' =>
        'sitemap',
    ),

    /*
        1. category/group
        2. category/maker
        3. category/hit
        4. category/new
        *category/param
        5. category/sort
        6. category/page

        7. category/group/maker
        8. category/group/hit
        9. category/group/new
        10. category/group/param
        11. category/group/sort
        12. category/group/page
        13. category/maker/hit
        14. category/maker/new
        *category/maker/param
        15. category/maker/sort
        16. category/maker/page
        17. category/hit/new
        *category/hit/param
        18. category/hit/sort
        19. category/hit/page
        *category/new/param
        20. category/new/sort
        21. category/new/page
        *category/param/sort
        *category/param/page
        22. category/sort/page

        23. category/group/maker/hit
        24. category/group/maker/new
        25. category/group/maker/param
        26. category/group/maker/sort
        27. category/group/maker/page
        28. category/group/hit/new
        29. category/group/hit/param
        30. category/group/hit/sort
        31. category/group/hit/page
        32. category/group/new/param
        33. category/group/new/sort
        34. category/group/new/page
        35. category/group/param/sort
        36. category/group/param/page
        37. category/group/sort/page
        38. category/maker/hit/new
        *category/maker/hit/param
        39. category/maker/hit/sort
        40. category/maker/hit/page
        *category/maker/new/param
        41. category/maker/new/sort
        42. category/maker/new/page
        *category/maker/param/sort
        *category/maker/param/page
        43. category/maker/sort/page
        *category/hit/new/param
        44. category/hit/new/sort
        45. category/hit/new/page
        *category/hit/param/sort
        *category/hit/param/page
        46. category/hit/sort/page
        *category/new/param/sort
        *category/new/param/page
        47. category/new/sort/page
        *category/param/sort/page

        48. category/group/maker/hit/new
        49. category/group/maker/hit/param
        50. category/group/maker/hit/sort
        51. category/group/maker/hit/page
        52. category/group/maker/new/param
        53. category/group/maker/new/sort
        54. category/group/maker/new/page
        55. category/group/maker/param/sort
        56. category/group/maker/param/page
        *category/maker/hit/new/param
        57. category/maker/hit/new/sort
        58. category/maker/hit/new/page
        *category/maker/new/param/sort
        *category/maker/new/param/page
        *category/maker/param/sort/page
        *category/hit/new/param/sort
        *category/hit/new/param/page
        *category/new/param/sort/page

        59. category/group/maker/hit/new/param
        60. category/group/maker/hit/new/sort
        61. category/group/maker/hit/new/page
        62. category/group/maker/hit/param/page
        63. category/group/maker/new/param/sort
        64. category/group/maker/new/param/page
        65. category/group/maker/new/sort/page
        *category/maker/hit/new/param/sort
        *category/maker/hit/new/param/page
        66. category/maker/hit/new/sort/page
        *category/maker/hit/param/sort/page
        *category/maker/new/param/sort/page

        67. category/group/maker/hit/new/param/sort
        68. category/group/maker/hit/new/param/page
        69. category/group/maker/hit/new/sort/page
        70. category/group/maker/hit/param/sort/page
        71. category/group/maker/new/param/sort/page
        72. category/group/hit/new/param/sort/page
        *category/maker/hit/new/param/sort/page

        73. category/group/maker/hit/new/param/sort/page
     */

    'sef2cap' => array( // Search Engines Friendly => Controller/Action/Params
        // главная страница сайта
        '~^$~' =>
        'frontend/index/index',

        /*
         * каталог
         */
        // главная страница каталога
        '~^catalog$~i' =>
        'frontend/catalog/index',
        // категория каталога
        '~^catalog/category/(\d+)$~i' =>
        'frontend/catalog/category/id/$1',

        // 1. категория каталога, фильтр по функционалу
        '~^catalog/category/(\d+)/group/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2',
        // 2. категория каталога, фильтр по производителю
        '~^catalog/category/(\d+)/maker/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2',
        // 3. категория каталога, лидеры продаж
        '~^catalog/category/(\d+)/hit/1$~i' =>
        'frontend/catalog/category/id/$1/hit/1',
        // 4. категория каталога, новинки
        '~^catalog/category/(\d+)/new/1$~i' =>
        'frontend/catalog/category/id/$1/new/1',
        // 5. категория каталога, сортировка
        '~^catalog/category/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/sort/$2',
        // 6. категория каталога, постраничная навигация
        '~^catalog/category/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/page/$2',
        // 7. категория каталога, фильтр по функционалу, фильтр по производителю
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3',
        // 8. категория каталога, фильтр по функционалу, лидеры продаж
        '~^catalog/category/(\d+)/group/(\d+)/hit/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1',
        // 9. категория каталога, фильтр по функционалу, новинки
        '~^catalog/category/(\d+)/group/(\d+)/new/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1',
        // 10. категория каталога, фильтр по функционалу, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/param/$3',
        // 11. категория каталога, фильтр по функционалу, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/sort/$3',
        // 12. категория каталога, фильтр по функционалу, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/page/$3',
        // 13. категория каталога, фильтр по производителю, лидеры продаж
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1',
        // 14. категория каталога, фильтр по производителю, новинки
        '~^catalog/category/(\d+)/maker/(\d+)/new/1$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/new/1',
        // 15. категория каталога, фильтр по производителю, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/sort/$3',
        // 16. категория каталога, фильтр по производителю, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/page/$3',
        // 17. категория каталога, лидеры продаж, новинки
        '~^catalog/category/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/category/id/$1/hit/1/new/1',
        // 18. категория каталога, лидеры продаж, сортировка
        '~^catalog/category/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/hit/1/sort/$2',
        // 19. категория каталога, лидеры продаж, постраничная навигация
        '~^catalog/category/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/hit/1/page/$2',
        // 20. категория каталога, новинки, сортировка
        '~^catalog/category/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/new/1/sort/$2',
        // 21. категория каталога, новинки, постраничная навигация
        '~^catalog/category/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/new/1/page/$2',
        // 22. категория каталога, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/sort/$2/page/$3',
        // 23. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1',
        // 24. категория каталога, фильтр по функционалу, фильтр по производителю, новинки
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1',
        // 25. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам
        '~^catalog/category/$1/group/$2/maker/$3/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1group/$2/maker/$3/param/$4',
        // 26. категория каталога, фильтр по функционалу, фильтр по производителю, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/sort/$4',
        // 27. категория каталога, фильтр по функционалу, фильтр по производителю, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/page/$4',
        // 28. категория каталога, фильтр по функционалу, лидеры продаж, новинки
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/category/id/(\d+)/group//hit/1/new/1',
        // 29. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/param/$3',
        // 30. категория каталога, фильтр по функционалу, лидеры продаж, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/sort/$3',
        // 31. категория каталога, фильтр по функционалу, лидеры продаж, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/page/$3',
        // 32. категория каталога, фильтр по функционалу, новинки, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/param/$3',
        // 33. категория каталога, фильтр по функционалу, новинки, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/sort/$3',
        // 34. категория каталога, фильтр по функционалу, новинки, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/page/$3',
        // 35. категория каталога, фильтр по функционалу, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/param/$3/sort/$4',
        // 36. категория каталога, фильтр по функционалу, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/param//page/$4',
        // 37. категория каталога, фильтр по функционалу, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/sort/$3/page/$4',
        // 38. категория каталога, фильтр по производителю, лидеры продаж, новинки
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/new/1',
        // 39. категория каталога, фильтр по производителю, лидеры продаж, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/sort/$3',
        // 40. категория каталога, фильтр по производителю, лидеры продаж, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/page/$3',
        // 41. категория каталога, фильтр по производителю, новинки, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/new/1/sort/$3',
        // 42. категория каталога, фильтр по производителю, новинки, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/new/1/page/$3',
        // 43. категория каталога, фильтр по производителю, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/sort/$3/page/$4',
        // 44. категория каталога, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/hit/1/new/1/sort/$2',
        // 45. категория каталога, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/hit/1/new/1/page/$2',
        // 46. категория каталога, лидеры продаж, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/hit/1/sort/$2/page/$3',
        // 47. категория каталога, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/new/1/sort/$2/page/$3',
        // 48. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1',
        // 49. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$4',
        // 50. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/sort/$4',
        // 51. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/page/$4',
        // 52. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4',
        // 53. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/sort/$4',
        // 54. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/page/$4',
        // 55. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/param/$4/sort/$5',
        // 56. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/param/$4/page/$5',
        // 57. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/maker//$2hit/1/new/1/sort/$3',
        // 58. категория каталога, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/new/1/page/$3',
        // 59. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4',
        // 60. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/sort/$4',
        // 61. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/page/$4',
        // 62. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$4/page/$5',
        // 63. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5',
        // 64. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4/page/$5',
        // 65. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/sort/$4/page/$5',
        // 66. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/new/1/sort/$3/page/$4',
        // 67. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5',
        // 68. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/page/$5',
        // 69. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/sort/$4/page/$5',
        // 70. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$5/sort/$5/page/$6',
        // 71. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4/sort/$5/page/$6',
        // 72. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$5',
        // 73. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5/page/$6',

        // страница товара каталога
        '~^catalog/product/(\d+)$~i' =>
        'frontend/catalog/product/id/$1',
        // страница со списком всех производителей
        '~^catalog/all-makers$~i' =>
        'frontend/catalog/allmkrs',
        // страница со списком товаров выбранного производителя
        '~^catalog/maker/(\d+)$~i' => 'frontend/catalog/maker/id/$1',
        // страница со списком товаров выбранного производителя, постраничная навигация
        '~^catalog/maker/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/page/$2',
        // страница со списком товаров выбранного производителя, сортировка
        '~^catalog/maker/(\d+)/sort/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/sort/$2',
        // страница со списком товаров выбранного производителя, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/sort/$2/page/$3',
        // страница поиска по каталогу
        '~^catalog/search$~i' =>
        'frontend/catalog/search',
        // страница результатов поиска по каталогу
        '~^catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'frontend/catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'frontend/catalog/search/query/$1/page/$2',

        // поиск по каталогу, XmlHttpRequest
        '~^catalog/ajax-search$~i' =>
        'frontend/catalog/ajaxsearch',

        // категория каталога, фильтр для выбранной категории, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1',
        // категория каталога, фильтр для выбранной категории, сортировка, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1/sort/$2',
        // категория каталога, фильтр по функционалу, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)/group/(\d+)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1/group/$2',
        // категория каталога, фильтр по функционалу, сортировка, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1/group/$2/sort/$3',
        // категория каталога, фильтр по производителю, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)/maker/(\d+)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1/maker/$2',
        // категория каталога, фильтр по производителю, сортировка, XmlHttpRequest
        '~^catalog/ajax-filter/category/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/ajaxfilter/category/$1/maker/$2/sort/$3',

        /*
         * просмотренные товары
         */
        // страница со списком всех просмотренных товаров
        '~^viewed$~i' =>
        'frontend/viewed/index',
        // просмотренные товары, постраничная навигация
        '~^viewed/page/(\d+)$~i' =>
        'frontend/viewed/index/page/$1',

        /*
         * отложенные товары
         */
        // страница со списком всех отложенных товаров
        '~^wished$~i' =>
        'frontend/wished/index',
        // отложенные товары, постраничная навигация
        '~^wished/page/(\d+)$~i' =>
        'frontend/wished/index/page/$1',
        // добавить товар в список отложенных
        '~^wished/addprd$~i' =>
        'frontend/wished/addprd',
        // удалить товар из списка отложенных
        '~^wished/rmvprd$~i' =>
        'frontend/wished/rmvprd',
        // добавить товар в список отложенных, XmlHttpRequest
        '~^wished/ajax/addprd$~i' =>
        'frontend/wished/ajax/action/addprd',
        // удалить товар из списка отложенных, XmlHttpRequest
        '~^wished/ajax/rmvprd$~i' =>
        'frontend/wished/ajax/action/rmvprd',

        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^compared$~i' =>
        'frontend/compared/index',
        // товары для сравнения, постраничная навигация
        '~^compared/page/(\d+)$~i' =>
        'frontend/compared/index/page/$1',
        // добавить товар в список сравнения
        '~^compared/addprd$~i' =>
        'frontend/compared/addprd',
        // удалить товар из списка сравнения
        '~^compared/rmvprd$~i' =>
        'frontend/compared/rmvprd',
        // добавить товар в список сравнения, XmlHttpRequest
        '~^compared/ajax/addprd$~i' =>
        'frontend/compared/ajax/action/addprd',
        // удалить товар из списка сравнения, XmlHttpRequest
        '~^compared/ajax/rmvprd$~i' =>
        'frontend/compared/ajax/action/rmvprd',

        /*
         * корзина
         */
        // страница покупательской корзины
        '~^basket$~i' =>
        'frontend/basket/index',
        // добавить товар в корзину
        '~^basket/addprd$~i' =>
        'frontend/basket/addprd',
        // удалить товар из корзины
        '~^basket/rmvprd/(\d+)$~i' =>
        'frontend/basket/rmvprd/id/$1',
        // оформление заказа
        '~^basket/checkout$~i' =>
        'frontend/basket/checkout',
        // добавить товар в корзину, XmlHttpRequest
        '~^basket/ajax/addprd$~i' =>
        'frontend/basket/ajax/action/addprd',

        /*
         * пользователи
         */
        // личный кабинет пользователя
        '~^user/index$~i' =>
        'frontend/user/index',
        // страница авторизации пользователя
        '~^user/login$~i' =>
        'frontend/user/login',
        // выйти из личного кабинета
        '~^user/logout$~i' =>
        'frontend/user/logout',
        // страница регистрации нового пользователя
        '~^user/reg$~i' =>
        'frontend/user/reg',
        // страница редактирования личных данных
        '~^user/edit$~i' =>
        'frontend/user/edit',
        // страница со списком профилей
        '~^user/allprof$~i' =>
        'frontend/user/allprof',
        // страница с формой для добавления профиля
        '~^user/addprof$~i' =>
        'frontend/user/addprof',
        // страница с формой для редактирования профиля
        '~^user/editprof/(\d+)$~i' =>
        'frontend/user/editprof/id/$1',
        // удаление профиля
        '~^user/rmvprof/(\d+)$~i' =>
        'frontend/user/rmvprof/id/$1',
        // восстановление пароля
        '~^user/forgot$~i' =>
        'frontend/user/forgot',
        // история заказов
        '~^user/all-orders$~i' =>
        'frontend/user/allorders',
        // история заказов, постраничная навигация
        '~^user/all-orders/page/(\d+)$~i' =>
        'frontend/user/allorders/page/$1',
        // подробная информация о заказе
        '~^user/order/(\d+)$~i' =>
        'frontend/user/order/id/$1',
        // повторить заказ
        '~^user/repeat/(\d+)$~i' =>
        'frontend/user/repeat/id/$1',
        // получение профиля, XmlHttpRequest
        '~^user/ajax/profile/(\d+)$~i' =>
        'frontend/user/ajax/id/$1',

        /*
         * новости
         */
        // главная страница новостей
        '~^news$~i' =>
        'frontend/news/index',
        // главная страница новостей, постраничная навигация
        '~^news/page/(\d+)$~i' =>
        'frontend/news/index/page/$1',
        // отдельная новость
        '~^news/item/(\d+)$~i' =>
        'frontend/news/item/id/$1',
        // список новостей выбранной категории
        '~^news/ctg/(\d+)$~i' =>
        'frontend/news/category/id/$1',
        // список новостей выбранной категории, постраничная навигация
        '~^news/ctg/(\d+)/page/(\d+)$~i' => 'frontend/news/category/id/$1/page/$2',

        /*
         * страницы сайта
         */
        '~^page/(\d+)$~i' =>
        'frontend/page/index/id/$1',

        /*
         * карта сайта
         */
        '~^sitemap$~i' =>
        'frontend/sitemap/index',
    )
);