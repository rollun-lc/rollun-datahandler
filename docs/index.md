
# rollun-datahandler

Библиотека `rollun-datahandler` расширяет стандартные библиотеки 
[`zendframework\zend-filter`](https://github.com/zendframework/zend-filter),
[`zendframework\zend-validator`](https://github.com/zendframework/zend-validator) и 
[`symfony\expression-language`](https://github.com/symfony/expression-language), 
а так же добавляет процессоры.

## Фильтры

Фильтры расширяют стандартную библиотеку [`zendframework\zend-filter`](https://github.com/zendframework/zend-filter).

**Список фильтров:**

* DuplicateSymbol - заменяет повторяющиеся символи (последовательность символов) на заданный символ (по умолчанию заменяет на то что дублируется)
* Evaluation - вычисляет выражение (Expression Language) над входящей строкой и возвращет его
* RemoveDigits - удаляет все цифры в строке, оставляя после себя один пробел ('a1c' => 'a c')
* RqlReplace - фильтр, который заменяет все вхождения rql маски на заданную строку
* SortSymbols - сортирует символы в строке (не имеет значение какие символы)
* SortWords - сортирует слова в строке

## Процессоры

Процессоры выполняют некоторые действие с входящим массивом. Суть процессора в том что бы 
он что то сделал с данными. Он может вернуть массив без изменений, при этом 
обработать данные и выполнить какое то действие над ними: записать в лог, отправить на почту и тд.

Пример:

```php
$processor = new Concat([
    'columns' => [1, 2],
    'delimiter' => '-'
    'resultColumn' => 3
]);

var_dump($processor->process(['a', 'b'])); // displays ['a', 'b', 'a-b']
```

**Список процессоров:**

* Concat - объединяет значения массива и записывает результат в столбец этого же массива
* Evaluation - вычисляет выражение над элементами массива (где ключ - название переменной в выражении, 
а значение - значение этой переменной) и записывает результат в столбец этого же массива
* FilterApplier - применяет фильтры к заданным столбцам массива

#### FilterApplier

Пример:

```php
$processor = new FilterApplier($options);

var_dump($processor->process(['1a2b3', 'b'])); // displays ['1a2b3', '321']
```

Пример масива `$options` для FilterApplier. [Подробнее о том как задавать опции используя фабрики](#factories).

```php
$options = [
    // Filters for applying
    // Key in array is a priority of filter
    'filters' => [
        0 => [
             'service' => 'digits',
        ],
        1 => [
            'service' => 'rqlReplace',
            'options' => [ // optional
                'pattern' => '123',
                'replacement' => '321',
            ],
        ],
    ],
    'argumentColumn' => 1,
    'resultColumn' => 2, // optional (will save to argumentColumn)
]
```

Все процессоры, в том числе и FilterApplier могут принимать валидатор вторым параметром.
Валидатор на входе получит тот же массив что и получил на входе процессор.
Если массив будет не валидный процессор обрабатывать его не будет.

```php
$validator = new EmailAddress();
$processor = new FilterApplier($options, $validator);

var_dump($processor->process(['1a2b3', '123'])); // displays ['1a2b3', 'b']
```

Для корректной работы валидатора, лучше использовать ArrayAdapter декоратор. Он применяет валидатор для заданого.
поля/полей

```php
$validator = ArrayAdapter([
    'columnsToValidate' => [1, 2],
    'validator' => 'digits',
]);
$processor = new FilterApplier($options, $validator);

var_dump($processor->process(['1a2b3', '123'])); // displays ['1a2b3', '321']
```

## Валидаторы

Валидаторы расширяют стандартную библиотеку [`zendframework\zend-validator`](https://github.com/zendframework/zend-validator).

Список валидаторов:

* ArrayAdapter - валидирует заданные столбцы массива

#### ArrayAdapter
```php
$array1 = ['abcd', '123'];
$array2 = ['321', '123'];

$validator = new ArrayValidator([
    'columnsToValidate' => [1, 2],
    'validator' => 'digits',
]);

var_dump($validator->isValid($array1)); // false
var_dump($validator->isValid($array2)); // true
```

Если для использования валидатора нужны дополнительные опции, их можна передать через `'validatorOptions'` ключ.
[Подробнее о том как задавать опции используя фабрики](#factories).

```php
$validator = new ArrayValidator([
    'columnsToValidate' => [1, 2],
    'validator' => 'inArray',
    'validatorOptions' => [
        'haystack' => $array2
    ],
]);

var_dump($validator->isValid($array2)); // false
```

## Язык выражений (Expression Language)

Расширение до библиотеки [`symfony\expression-language`](https://github.com/symfony/expression-language).
Компонент ExpressionLanguage может компилировать и вычислять выражения.

Пример:

```php
$expressionLanguage = new ExpressionLanguage();

var_dump($expressionLanguage->evaluate('1 + 2')); // displays 3
var_dump($expressionLanguage->compile('1 + 2')); // displays (1 + 2)

// Array for variables in expression
$values = [
    'a' => 2,
    'b' => 5,
];
var_dump($expressionLanguage->evaluate('a * b'), $values)); // displays 10
var_dump($expressionLanguage->compile('a * b'), $values)); // displays (2 * 5)
```

#### Expression functions

Callback - предоставляет возможность создание ExpressionFunction с колбэка.
Такая функция не может быть скомпилирована, поэтому при попытки компиляции выражения которое использует это функцию
будет выброшено исключение.

```php
$callback = function($value) {
    return $value . $value;
};
$expressionFunction = new ExpressionFunction\Callback($callback, 'foo');
$expressionLanguage = new ExpressionLanguage();
$expressionLanguage->addFunction($expressionFunction);

var_dump($expressionLanguage->evaluate("foo('a')")); // displays 'aa'
var_dump($expressionLanguage->compile("foo('a')")); // exception will be thrown
```

#### Expression function providers

PluginExpressionFunctionProvider - предоставляет возможность создание ExpressionFunctionProvider с AbstractPluginManager,
указав список сервисов и вызываемый метод.

```php
$pluginManager = new FilterPluginManager(new ServiceManager());
$services = ['digits', 'stringTrim'];
$expressionFunctionProvider = new PluginExpressionFunctionProvider($pluginManager, $services, 'filter');
$expressionLanguage = new ExpressionLanguage();
$expressionLanguage->registerProvider($expressionFunctionProvider);

var_dump($expressionLanguage->evaluate("digits('123abc')")); // displays '123'
var_dump($expressionLanguage->compile("stringTrim('   ad   ')")); displays 'ad'
```

## Factories

Процессоры, валидаторы и фильтры (далее 'плагины') могут бить созданы как с помощью плагин менеджера, 

```php
$filterPluginManager = FilterPluginManager(new ServiceManager());
// Create filter using filter plugin options
$filter = $filterPluginManager->get('pregReplace', [
   'pattern' => '/aaa/',
   'replacement' => 'a',
]);

var_dump(get_class($filter)); // Zend\Filter\PregReplace
```

так и с непосредственно через контейнер. 

```php
$container = new ServiceManager();
$container->setService('config', [
    'filters' => [
        'abstract_factory_config' => [
            SimpleFilterAbstractFactory::class => [
                'pregReplaceFilter' => [
                    'class' => PregReplace::class,
                    'options' => [
                        'pattern' => '/aaa/',
                        'replacement' => 'a',
                    ],
                ]
            ]
        ]
    ]
]);

$filterPluginManager = FilterPluginManager($container);
$filter = $filterPluginManager->get('pregReplace');

var_dump(get_class($filter)); // Zend\Filter\PregReplace
```

Если конфигурации для плагина заданы и в конфигах контейнера и через $options
при создание через плагин менеджер, то они не должны конфликтовать, иначе будет выброшено исключение.

```php
$container = new ServiceManager();
$container->setService('config', [
    'filters' => [
        'abstract_factory_config' => [
            SimpleFilterAbstractFactory::class => [
                'pregReplaceFilter' => [
                    'class' => PregReplace::class,
                    'options' => [
                        'pattern' => '/aaa/',
                        'replacement' => 'a',
                    ],
                ]
            ]
        ]
    ]
]);
$filterPluginManager = FilterPluginManager($container);

// Will be thrown exception
$filter = $filterPluginManager->get('pregReplace', [
   'pattern' => '/aaa/',
   'replacement' => 'a',
]);
```

UPD: fake changes for testing
