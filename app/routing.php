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
        // 57. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/param/$3',
        // 58. категория каталога, фильтр по функционалу, лидеры продаж, новинка, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/sort/$3',
        // 59. категория каталога, фильтр по функционалу, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/page/$3',
        // 60. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/hit/1/param/$3/sort/$4',
        // 61. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/param/$3/page/$4',
        // 62. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/sort/$3/page/$4',
        // 63. категория каталога, фильтр по функционалу, новинка, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/new/1/param/$3/sort/$4',
        // 64. категория каталога, фильтр по функционалу, новинка, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/new/1/param/$3/page/$4',
        // 65. категория каталога, фильтр по функционалу, новинка, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/new/1/sort/$3/page/$4',
        // 66. категория каталога, фильтр по функционалу, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/param/$3/sort/$4/page/$5',
        // 67. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/sort/$3',
        // 68. категория каталога, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/page/$3',
        // 69. категория каталога, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/new/1/sort/$3/page/$4',
        // 70. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4',
        // 71. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки,
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/sort/$4',
        // 72. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/page/$4',
        // 73. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5',
        // 74. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/page/$5',
        // 75. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4/sort/$5',
        // 76. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4/page/$5',
        // 77. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/sort/$4/page/$5',
        // 78. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/param/$4/sort/$5/page/$6',
        // 79. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/param/$3/sort/$4',
        // 80. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/param/$3/page/$4',
        // 81. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/param/$3/sort/$4/page/$5',
        // 82. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/new/1/param/$3/sort/$4/page/$5',
        // 83. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/hit/1/new/1/sort/$3/page/$4',
        // 84. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5',
        // 85. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/page/$5',
        // 86. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/sort/$4/page/$5',
        // 87. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5/page/$6',
        // 88. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/new/1/param/$4/sort/$5/page/$6',
        // 89. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$5',
        // 90. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5/page/$6',
        
        // товары производителя
        '~^frontend/catalog/maker/id/(\d+)$~i' =>
        'catalog/maker/$1',
        
        // 1. товары производителя, фильтр по функционалу
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)$~i' =>
        'catalog/maker/$1/group/$2',
        // 2. товары производителя, лидеры продаж
        '~^frontend/catalog/maker/id/(\d+)/hit/1$~i' =>
        'catalog/maker/$1/hit/1',
        // 3. товары производителя, лидеры продаж
        '~^frontend/catalog/maker/id/(\d+)/new/1$~i' =>
        'catalog/maker/$1/new/1',
        // 4. товары производителя, сортировка
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)$~i' =>
        'catalog/maker/$1/sort/$2',
        // 5. товары производителя, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/page/(\d+)$~i' =>
        'catalog/maker/$1/page/$2',
        // 6. товары производителя, фильтр по функционалу, лидеры продаж
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1$~i' =>
        'catalog/maker/$1/group/$2/hit/1',
        // 7. товары производителя, фильтр по функционалу, новинки
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1$~i' =>
        'catalog/maker/$1/group/$2/new/1',
        // 8. товары производителя, фильтр по функционалу, фильтр по параметрам
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/maker/$1/group/$2/param/$3',
        // 9. товары производителя, фильтр по функционалу, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/sort/$3',
        // 10. товары производителя, фильтр по функционалу, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/page/$3',
        // 11. товары производителя, лидеры продаж, новинки
        '~^frontend/catalog/maker/id/(\d+)/hit/1/new/1$~i' =>
        'catalog/maker/$1/hit/1/new/1',
        // 12. товары производителя, лидеры продаж, сортировка
        '~^frontend/catalog/maker/id/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/maker/$1/hit/1/sort/$2',
        // 13. товары производителя, лидеры продаж, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/maker/$1/hit/1/page/$2',
        // 14. товары производителя, новинки, сортировка
        '~^frontend/catalog/maker/id/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/maker/$1/new/1/sort/$2',
        // 15. товары производителя, новинки, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/maker/$1/new/1/page/$2',
        // 16. товары производителя, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/sort/$2/page/$3',
        // 17. товары производителя, фильтр по функционалу, лидеры продаж, новинки
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1',
        // 18. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3',
        // 19. товары производителя, фильтр по функционалу, лидеры продаж, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/sort/$3',
        // 20. товары производителя, фильтр по функционалу, лидеры продаж, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/page/$3',
        // 21. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/maker/$1/group/$2/new/1/param/$3',
        // 22. товары производителя, фильтр по функционалу, новинки, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/new/1/sort/$3',
        // 23. товары производителя, фильтр по функционалу, новинки, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/new/1/page/$3',
        // 24. товары производителя, фильтр по функционалу, фильтр по параметрам, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/param/$3/sort/$4',
        // 25. товары производителя, фильтр по функционалу, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/param/$3/page/$4',
        // 26. товары производителя, фильтр по функционалу, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/sort/$3/page/$4',
        // 27. товары производителя, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/maker/id/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/maker/$1/hit/1/new/1/sort/$2',
        // 28. товары производителя, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/maker/$1/hit/1/new/1/page/$2',
        // 29. товары производителя, лидеры продаж, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/hit/1/sort/$2/page/$3',
        // 30. товары производителя, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/new/1/sort/$2/page/$3',
        // 31. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/param/$3',
        // 32. товары производителя, фильтр по функционалу, лидеры продаж, новинки, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/sort/$3',
        // 33. товары производителя, фильтр по функционалу, лидеры продаж, новинки, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/page/$3',
        // 34. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3/sort/$4', 
        // 35. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3/page/$4', 
        // 36. товары производителя, фильтр по функционалу, лидеры продаж, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/sort/$3/page/$4',
        // 37. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3/sort/$4',
        // 38. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3/page/$4',
        // 39. товары производителя, фильтр по функционалу, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/new/1/sort/$3/page/$4',
        // 40. товары производителя, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/hit/1/new/1/sort/$2/page/$3',
        
        // 41. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/param/$3/sort/$4',
        // 42. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/param/$3/page/$4', 
        // 43. товары производителя, фильтр по функционалу, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/sort/$3/page/$4',
        // 44. товары производителя, фильтр по функционалу, лидеры продаж, фиильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/param/$3/sort/$4/page/$5',
        // 45. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/new/1/param/$3/sort/$4/page/$5',
        // 46. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$5',

        // страница товара каталога
        '~^frontend/catalog/product/id/(\d+)$~i' =>
        'catalog/product/$1',
        // страница со списком производителей
        '~^frontend/catalog/allmkrs$~i' =>
        'catalog/all-makers',
        // страница поиска по каталогу
        '~^frontend/catalog/search$~i'=>
        'catalog/search',
        // страница результатов поиска по каталогу
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'catalog/search/query/$1/page/$2',

        // подгрузка меню каталога, XmlHttpRequest
        '~^frontend/catalog/menu$~i' =>
        'catalog/menu',

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
        // добавить комментарий к товару из списка отложенных
        '~^frontend/wished/comment$~i' =>
        'wished/comment',

        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^frontend/compare/index$~i' =>
        'compare',
        // страница со списком товаров для сравнения, таблица
        '~^frontend/compare/table$~i' =>
        'compare/table',
        // товары для сравнения, постраничная навигация
        '~^frontend/compare/index/page/(\d+)$~i' =>
        'compare/page/$1',
        // добавить товар в список сравнения
        '~^frontend/compare/addprd$~i' =>
        'compare/addprd',
        // удалить товар из списка сравнения
        '~^frontend/compare/rmvprd$~i' =>
        'compare/rmvprd',
        // удалить все товары из сравнения
        '~^frontend/compare/clear$~i' =>
        'compare/clear',

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
        // удалить все товары из корзины
        '~^frontend/basket/clear$~i' =>
        'basket/clear',
        // оформление заказа
        '~^frontend/basket/checkout$~i' =>
        'basket/checkout',

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
        '~^frontend/user/allorders$~i' =>
        'user/all-orders',
        // история заказов, постраничная навигация
        '~^frontend/user/allorders/page/(\d+)$~i' =>
        'user/all-orders/page/$1',
        // подробная информация о заказе
        '~^frontend/user/order/id/(\d+)$~i' =>
        'user/order/$1',
        // повторить заказ
        '~^frontend/user/repeat/id/(\d+)$~i' =>
        'user/repeat/$1',
        // получение профиля для страницы оформления заказа
        '~^frontend/user/profile/id/(\d+)$~i' =>
        'user/profile/$1',

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
        'news/category/$1',
        // список новостей выбранной категории, постраничная навигация
        '~^frontend/news/category/id/(\d+)/page/(\d+)$~i' =>
        'news/category/$1/page/$2',

        /*
         * блог
         */
        // главная страница блога
        '~^frontend/blog/index$~i' =>
        'blog',
        // главная страница блога, постраничная навигация
        '~^frontend/blog/index/page/(\d+)$~i' =>
        'blog/page/$1',
        // отдельный пост блога
        '~^frontend/blog/post/id/(\d+)$~i' =>
        'blog/item/$1',
        // список постов блога выбранной категории
        '~^frontend/blog/category/id/(\d+)$~i' =>
        'blog/category/$1',
        // список постов блога выбранной категории, постраничная навигация
        '~^frontend/blog/category/id/(\d+)/page/(\d+)$~i' =>
        'blog/category/$1/page/$2',

        /*
         * типовые решения
         */
        // главная страница типовых решений
        '~^frontend/solutions/index$~i' =>
        'solutions',
        // главная страница типовых решений, постраничная навигация
        '~^frontend/solutions/index/page/(\d+)$~i' =>
        'solutions/page/$1',
        // отдельное типовое решение
        '~^frontend/solutions/item/id/(\d+)$~i' =>
        'solutions/item/$1',
        // список типовых решений выбранной категории
        '~^frontend/solutions/category/id/(\d+)$~i' =>
        'solutions/category/$1',
        // список типовых решений выбранной категории, постраничная навигация
        '~^frontend/solutions/category/id/(\d+)/page/(\d+)$~i' =>
        'solutions/category/$1/page/$2',
        // добавление товаров типового решения в корзину
        '~^frontend/solutions/basket/id/(\d+)$~i' =>
        'solutions/basket/$1',

        /*
         * распродажа
         */
        '~^frontend/sale/index$~i' =>
        'sale',
        
        /*
         * рейтинг продаж
         */
        '~^frontend/rating/index$~i' =>
        'rating',

        /*
         * карта сайта
         */
        '~^frontend/sitemap/index$~i' =>
        'sitemap',

        /*
         * обмен данными с 1С
         */
        '~^frontend/exchange/neworders/access/([a-f0-9]{32})$~i' =>
        'exchange/get-new-orders/access/$1',
        '~^frontend/exchange/order/id/(\d+)/access/([a-f0-9]{32})$~i' =>
        'exchange/get-order/id/$1/access/$2',
        '~^frontend/exchange/setstatus/order/(\d+)/status/(\d+)/access/([a-f0-9]{32})$~i' =>
        'exchange/set-order-status/order/$1/status/$2/access/$3',
    ),

    /*
        КАТЕГОРИЯ КАТАЛОГА
    
        1. category/group
        2. category/maker
        3. category/hit
        4. category/new
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
        15. category/maker/sort
        16. category/maker/page
        17. category/hit/new
        18. category/hit/sort
        19. category/hit/page
        20. category/new/sort
        21. category/new/page
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
        39. category/maker/hit/sort
        40. category/maker/hit/page
        41. category/maker/new/sort
        42. category/maker/new/page
        43. category/maker/sort/page
        44. category/hit/new/sort
        45. category/hit/new/page
        46. category/hit/sort/page
        47. category/new/sort/page

        48. category/group/maker/hit/new
        49. category/group/maker/hit/param
        50. category/group/maker/hit/sort
        51. category/group/maker/hit/page
        52. category/group/maker/new/param
        53. category/group/maker/new/sort
        54. category/group/maker/new/page
        55. category/group/maker/param/sort
        56. category/group/maker/param/page
        57. category/group/hit/new/param
        58. category/group/hit/new/sort
        59. category/group/hit/new/page
        60. category/group/hit/param/sort
        61. category/group/hit/param/page
        62. category/group/hit/sort/page
        63. category/group/new/param/sort
        64. category/group/new/param/page
        65. category/group/new/sort/page
        66. category/group/param/sort/page
        67. category/maker/hit/new/sort
        68. category/maker/hit/new/page
        69. category/maker/new/sort/page

        70. category/group/maker/hit/new/param
        71. category/group/maker/hit/new/sort
        72. category/group/maker/hit/new/page
        73. category/group/maker/hit/param/sort
        74. category/group/maker/hit/param/page
        75. category/group/maker/new/param/sort
        76. category/group/maker/new/param/page
        77. category/group/maker/new/sort/page
        78. category/group/maker/param/sort/page
        79. category/group/hit/new/param/sort
        80. category/group/hit/new/param/page
        81. category/group/hit/param/sort/page
        82. category/group/new/param/sort/page
        83. category/maker/hit/new/sort/page

        84. category/group/maker/hit/new/param/sort
        85. category/group/maker/hit/new/param/page
        86. category/group/maker/hit/new/sort/page
        87. category/group/maker/hit/param/sort/page
        88. category/group/maker/new/param/sort/page
        89. category/group/hit/new/param/sort/page

        90. category/group/maker/hit/new/param/sort/page
     */
     
    /*
        ТОВАРЫ ПРОИЗВОДИТЕЛЯ
        
        1.  maker/group
        2.  maker/hit
        3.  maker/new
        4.  maker/sort
        5.  maker/page
        
        6.  maker/group/hit
        7.  maker/group/new
        8.  maker/group/param
        9.  maker/group/sort
        10.  maker/group/page
        11. maker/hit/new
        12. maker/hit/sort
        13. maker/hit/page
        14. maker/new/sort
        15. maker/new/page
        16. maker/sort/page
        
        17. maker/group/hit/new
        18. maker/group/hit/param
        19. maker/group/hit/sort
        20. maker/group/hit/page
        21. maker/group/new/param
        22. maker/group/new/sort
        23. maker/group/new/page
        24. maker/group/param/sort
        25. maker/group/param/page
        26. maker/group/sort/page
        27. maker/hit/new/sort
        28. maker/hit/new/page
        29. maker/hit/sort/page
        30. maker/new/sort/page
        
        31. maker/group/hit/new/param
        32. maker/group/hit/new/sort
        33. maker/group/hit/new/page
        34. maker/group/hit/param/sort
        35. maker/group/hit/param/page
        36. maker/group/hit/sort/page
        37. maker/group/new/param/sort
        38. maker/group/new/param/page
        39. maker/group/new/sort/page
        40. maker/hit/new/sort/page
        
        41. maker/group/hit/new/param/sort
        42. maker/group/hit/new/param/page
        43. maker/group/hit/new/sort/page
        44. maker/group/hit/param/sort/page
        45. maker/group/new/param/sort/page

        46. maker/group/hit/new/param/sort/page
        
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
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/param/$4',
        // 26. категория каталога, фильтр по функционалу, фильтр по производителю, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/sort/$4',
        // 27. категория каталога, фильтр по функционалу, фильтр по производителю, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/page/$4',
        // 28. категория каталога, фильтр по функционалу, лидеры продаж, новинки
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1',
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
        'frontend/catalog/category/id/$1/group/$2/param/$3/page/$4',
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
        // 57. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/param/$3',
        // 58. категория каталога, фильтр по функционалу, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/sort/$3',
        // 59. категория каталога, фильтр по функционалу, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/page/$3',
        // 60. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/param/$3/sort/$4',
        // 61. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/param/$3/page/$4',
        // 62. категория каталога, фильтр по функционалу, лидеры продаж, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/sort/$3/page/$4',
        // 63. категория каталога, фильтр по функционалу, новинки, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/param/$3/sort/$4',
        // 64. категория каталога, фильтр по функционалу, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/param/$3/page/$4',
        // 65. категория каталога, фильтр по функционалу, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/sort/$3/page/$4',
        // 66. категория каталога, фильтр по функционалу, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/param/$3/sort/$4/page/$5',
        // 67. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/maker//$2hit/1/new/1/sort/$3',
        // 68. категория каталога, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/new/1/page/$3',
        // 69. категория каталога, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/new/1/sort/$3/page/$4',
        // 70. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4',
        // 71. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/sort/$4',
        // 72. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/page/$4',
        // 73. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$4/sort/$5',
        // 74. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$4/page/$5',
        // 75. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4/sort/$5',
        // 76. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4/page/$5',
        // 77. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/sort/$4/page/$5',
        // 78. категория каталога, фильтр по функционалу, фильтр по производителю, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/param/$4/sort/$5/page/$6',
        // 79. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/param/$3/sort/$4',
        // 80. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/param/$3/page/$4',
        // 81. категория каталога, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/param/$3/sort/$4/page/$5',
        // 82. категория каталога, фильтр по функционалу, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/new/1/param/$3/sort/$4/page/$5',
        // 83. категория каталога, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/maker/$2/hit/1/new/1/sort/$3/page/$4',
        // 84. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5',
        // 85. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/page/$5',
        // 86. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/sort/$4/page/$5',
        // 87. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/param/$5/sort/$5/page/$6',
        // 88. категория каталога, фильтр по функционалу, фильтр по производителю, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/new/1/param/$4/sort/$5/page/$6',
        // 89. категория каталога, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$5',
        // 90. категория каталога, фильтр по функционалу, фильтр по производителю, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/group/(\d+)/maker/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/category/id/$1/group/$2/maker/$3/hit/1/new/1/param/$4/sort/$5/page/$6',
        
        // товары производителя
        '~^catalog/maker/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1',
        
        // 1. товары производителя, фильтр по функционалу
        '~^catalog/maker/(\d+)/group/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2',
        // 2. товары производителя, лидеры продаж
        '~^catalog/maker/(\d+)/hit/1$~i' =>
        'frontend/catalog/maker/id/$1/hit/1',
        // 3. товары производителя, новинки
        '~^catalog/maker/(\d+)/new/1$~i' =>
        'frontend/catalog/maker/id/$1/new/1',
        // 4. товары производителя, сортировка
        '~^catalog/maker/(\d+)/sort/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/sort/$2',
        // 5. товары производителя, постраничная навигация
        '~^catalog/maker/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/page/$2',
        // 6. товары производителя, фильтр по функционалу, лидеры продаж
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1',
        // 7. товары производителя, фильтр по функционалу, новинки
        '~^catalog/maker/(\d+)/group/(\d+)/new/1$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1',
        // 8. товары производителя, фильтр по функционалу, фильтр по параметрам
        '~^catalog/maker/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/param/$3',   
        // 9. товары производителя, фильтр по функционалу, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/sort/$3',
        // 10. товары производителя, фильтр по функционалу, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/page/$3',
        // 11. товары производителя, лидеры продаж, новинки
        '~^catalog/maker/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/new/1',
        // 12. товары производителя, лидеры продаж, сортировка
        '~^catalog/maker/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/sort/$2',
        // 13. товары производителя, лидеры продаж, постраничная навигация
        '~^catalog/maker/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/page/$2',
        // 14. товары производителя, новинки, сортировка
        '~^catalog/maker/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/new/1/sort/$2',
        // 15. товары производителя, новинки, постраничная навигация
        '~^catalog/maker/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/new/1/page/$2',
        // 16. товары производителя, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/sort/$2/page/$3',
        // 17. товары производителя, фильтр по функционалу, лидеры продаж, новинки
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1',
        // 18. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/param/$3',
        // 19. товары производителя, фильтр по функционалу, лидеры продаж, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/sort/$3',
        // 20. товары производителя, фильтр по функционалу, лидеры продаж, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/page/$3',
        // 21. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/param/$3',
        // 22. товары производителя, фильтр по функционалу, новинки, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/sort/$3',
        // 23. товары производителя, фильтр по функционалу, новинки, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/page/$3',
        // 24. товары производителя, фильтр по функционалу, фильтр по параметрам, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/param/$3/sort/$4',
        // 25. товары производителя, фильтр по функционалу, фильтр по параметрам,постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/param/$3/page/$4',
        // 26. товары производителя, фильтр по функционалу, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/sort/$3/page/$4',
        // 27. товары производителя, лидеры продаж, новинки, сортировка
        '~^catalog/maker/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/new/1/sort/$2',
        // 28. товары производителя, лидеры продаж, новинки, постраничная навигация
        '~^catalog/maker/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/new/1/page/$2',
        // 29. товары производителя, лидеры продаж, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/sort/$2/page/$3',
        // 30. товары производителя, новинки, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/new/1/sort/$2/page/$3',
        // 31. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/param/$3',
        // 32. товары производителя, фильтр по функционалу, лидеры продаж, новинки, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/sort/$3',
        // 33. товары производителя, фильтр по функционалу, лидеры продаж, новинки, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/page/$3',
        // 34. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/param/$3/sort/$4',
        // 35. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/param/$3/page/$4',
        // 36. товары производителя, фильтр по функционалу, лидеры продаж, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/sort/$3/page/$4',
        // 37. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/param/$3/sort/$4',
        // 38. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/param/$3/page/$4',
        // 39. товары производителя, фильтр по функционалу, новинки, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/sort/$3/page/$4',
        // 40. товары производителя, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/hit/1/new/1/sort/$2/page/$3',
        
        // 41. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/param/$3/sort/$4',
        // 42. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/param/$3/page/$4',
        // 43. товары производителя, фильтр по функционалу, лидеры продаж, новинки, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/sort/$3/page/$4',
        // 44. товары производителя, фильтр по функционалу, лидеры продаж, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/param/$3/sort/$4/page/$4',
        // 45. товары производителя, фильтр по функционалу, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/new/1/param/$3/sort/$4/page/$4',
        // 46. товары производителя, фильтр по функционалу, лидеры продаж, новинки, фильтр по параметрам, сортировка, постраничная навигация
        '~^catalog/maker/(\d+)/group/(\d+)/hit/1/new/1/param/(\d+\.\d+(?:-\d+\.\d+)*)/sort/(\d)/page/(\d+)$~i' =>
        'frontend/catalog/maker/id/$1/group/$2/hit/1/new/1/param/$3/sort/$4/page/$4',

        // страница товара каталога
        '~^catalog/product/(\d+)$~i' =>
        'frontend/catalog/product/id/$1',
        // страница со списком всех производителей
        '~^catalog/all-makers$~i' =>
        'frontend/catalog/allmkrs',
        // страница поиска по каталогу
        '~^catalog/search$~i' =>
        'frontend/catalog/search',
        // страница результатов поиска по каталогу
        '~^catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'frontend/catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'frontend/catalog/search/query/$1/page/$2',

        // подгрузка меню каталога, XmlHttpRequest
        '~^catalog/menu$~i' =>
        'frontend/catalog/menu',

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
        // добавить комментарий к товару из списка отложенных
        '~^wished/comment$~i' =>
        'frontend/wished/comment',

        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^compare$~i' =>
        'frontend/compare/index',
        // страница со списком товаров для сравнения, таблица
        '~^compare/table$~i' =>
        'frontend/compare/table',
        // товары для сравнения, постраничная навигация
        '~^compare/page/(\d+)$~i' =>
        'frontend/compare/index/page/$1',
        // добавить товар в список сравнения
        '~^compare/addprd$~i' =>
        'frontend/compare/addprd',
        // удалить товар из списка сравнения
        '~^compare/rmvprd$~i' =>
        'frontend/compare/rmvprd',
        // удалить все товары из сравнения
        '~^compare/clear$~i' =>
        'frontend/compare/clear',

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
        // удалить все товары из корзины
        '~^basket/clear$~i' =>
        'frontend/basket/clear',
        // оформление заказа
        '~^basket/checkout$~i' =>
        'frontend/basket/checkout',

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
        // получение профиля для страницы оформления заказа
        '~^user/profile/(\d+)$~i' =>
        'frontend/user/profile/id/$1',

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
        '~^news/category/(\d+)$~i' =>
        'frontend/news/category/id/$1',
        // список новостей выбранной категории, постраничная навигация
        '~^news/category/(\d+)/page/(\d+)$~i' =>
        'frontend/news/category/id/$1/page/$2',

        /*
         * блога
         */
        // главная страница блога
        '~^blog$~i' =>
        'frontend/blog/index',
        // главная страница блога, постраничная навигация
        '~^blog/page/(\d+)$~i' =>
        'frontend/blog/index/page/$1',
        // отдельный пост блога
        '~^blog/item/(\d+)$~i' =>
        'frontend/blog/post/id/$1',
        // список постов блога выбранной категории
        '~^blog/category/(\d+)$~i' =>
        'frontend/blog/category/id/$1',
        // список постов блога выбранной категории, постраничная навигация
        '~^blog/category/(\d+)/page/(\d+)$~i' =>
        'frontend/blog/category/id/$1/page/$2',

        /*
         * типовые решения
         */
        // главная страница типовых решений
        '~^solutions$~i' =>
        'frontend/solutions/index',
        // главная страница типовых решений, постраничная навигация
        '~^solutions/page/(\d+)$~i' =>
        'frontend/solutions/index/page/$1',
        // отдельное типовое решение
        '~^solutions/item/(\d+)$~i' =>
        'frontend/solutions/item/id/$1',
        // список типовых решений выбранной категории
        '~^solutions/category/(\d+)$~i' =>
        'frontend/solutions/category/id/$1',
        // список типовых решений выбранной категории, постраничная навигация
        '~^solutions/category/(\d+)/page/(\d+)$~i' =>
        'frontend/solutions/category/id/$1/page/$2',
        // добавление товаров типового решения в корзину
        '~^solutions/basket/(\d+)$~i' =>
        'frontend/solutions/basket/id/$1',

        /*
         * распродажа
         */
        '~^sale$~i' =>
        'frontend/sale/index$1',
        
        /*
         * рейтинг продаж
         */
        '~^rating$~i' =>
        'frontend/rating/index$1',

        /*
         * карта сайта
         */
        '~^sitemap$~i' =>
        'frontend/sitemap/index',

        /*
         * обмен данными с 1С
         */
        '~^exchange/get-new-orders/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/neworders/access/$1',
        '~^exchange/get-order/id/(\d+)/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/order/id/$1/access/$2',
        '~^exchange/set-order-status/order/(\d+)/status/(\d+)/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/setstatus/order/$1/status/$2/access/$3',
    )
);