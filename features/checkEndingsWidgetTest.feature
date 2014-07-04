Feature: Добавление блока с виджетом проверки окончаний

  Scenario: Создаем новый блок, активируем виджет проверки окончаний, добавляем слова и окончания, проверяем результат
    Given Открываем страницу "http://localhost"
    When  Создаем модуль с заголовком "Module title", ключевыми вопросами "key questions" и описанием "description"
    And   Создаем блок с заголовком "Block title"
    And   Выбираем виджет со значением "check-endings"
    Then  Добавляем окончание "га"
    And   Добавляем окончания "го, ге, ка, ко, ке"
    And   Добавляем слово "слово" с окончанием "га"
    And   Добавляем слово "слово 2" с окончанием "ге"
    And   Добавляем слово "слово 3" с окончанием "го"
    And   Добавляем слово "слово 4" с окончанием "ка"
    And   Добавляем слово "слово 5" с окончанием "ке"
    And   Завершаем редактирование блока
    And   Проверяем слово "слово 5" с окончанием "ке"
    And   Проверяем слово "слово 2" с окончанием "ге"
    And   Проверяем слово "слово 4" с окончанием "ка"