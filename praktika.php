<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

// Разбиение и объединение ФИО
// Исправленная функция getFullnameFromParts
function getFullnameFromParts($surname, $name, $patronymic)
{
    return $surname . ' ' . $name . ' ' . $patronymic;
}

function getPartsFromFullName($fullname)
{
    $parts = explode(' ', $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronymic' => $parts[2]
    ];
}

//Сокращение ФИО
function getShortName($fullname)
{
    $parts = getPartsFromFullName($fullname);
    return $parts['name'] . ' ' . mb_substr($parts['surname'], 0, 1) . '.';
}

//Определение пола по ФИО
function getGenderFromName($fullname)
{
    $parts = getPartsFromFullName($fullname);
    $genderScore = 0;

    // Признаки мужского пола
    if (mb_substr($parts['patronymic'], -2) === 'ич') {
        $genderScore++;
    }
    if (mb_substr($parts['name'], -1) === 'й' || mb_substr($parts['name'], -1) === 'н') {
        $genderScore++;
    }
    if (mb_substr($parts['surname'], -1) === 'в') {
        $genderScore++;
    }

    // Признаки женского пола
    if (mb_substr($parts['patronymic'], -3) === 'вна') {
        $genderScore--;
    }
    if (mb_substr($parts['name'], -1) === 'а') {
        $genderScore--;
    }
    if (mb_substr($parts['surname'], -2) === 'ва') {
        $genderScore--;
    }

    // Определение пола
    if ($genderScore > 0) {
        return 'Мужской пол';
    } elseif ($genderScore < 0) {
        return 'Женский пол';
    } else {
        return 'Неопределенный пол';
    }
}

//Определение возрастно-полового состава
function getGenderDescription($personsArray)
{
    $genderCounts = ['Мужской пол' => 0, 'Женский пол' => 0, 'Неопределенный пол' => 0];
    $totalPersons = count($personsArray);

    foreach ($personsArray as $person) {
        $gender = getGenderFromName($person['fullname']);
        if ($gender === 'Мужской пол') {
            $genderCounts['Мужской пол']++;
        } elseif ($gender === 'Женский пол') {
            $genderCounts['Женский пол']++;
        } else {
            $genderCounts['Неопределенный пол']++;
        }
    }

    // Расчет процентов
    $malePercentage = ($genderCounts['Мужской пол'] / $totalPersons) * 100;
    $femalePercentage = ($genderCounts['Женский пол'] / $totalPersons) * 100;
    $undefinedPercentage = ($genderCounts['Неопределенный пол'] / $totalPersons) * 100;

    return "Гендерный состав аудитории:\n" .
        "Мужчины - " . round($malePercentage, 1) . "%\n" .
        "Женщины - " . round($femalePercentage, 1) . "%\n" .
        "Не удалось определить - " . round($undefinedPercentage, 1) . "%";
}

//Идеальный подбор пары
// Исправленная функция getPerfectPartner
function getPerfectPartner($surname, $name, $patronymic, $personsArray)
{
    // Нормализация фамилии, имени и отчества
    $normalizedFullName = mb_convert_case($surname, MB_CASE_TITLE, "UTF-8") . ' ' .
        mb_convert_case($name, MB_CASE_TITLE, "UTF-8") . ' ' .
        mb_convert_case($patronymic, MB_CASE_TITLE, "UTF-8");

    $ownGender = getGenderFromName($normalizedFullName);
    if ($ownGender === 'Неопределенный пол') {
        return "Пол для '{$normalizedFullName}' не может быть определен";
    }

    do {
        $randomPerson = $personsArray[rand(0, count($personsArray) - 1)];
        $randomPersonFullName = getFullnameFromParts($randomPerson['surname'], $randomPerson['name'], $randomPerson['patronymic']);
        $randomPersonGender = getGenderFromName($randomPersonFullName);
    } while ($ownGender === $randomPersonGender || $randomPersonGender === 'Неопределенный пол');

    $compatibility = rand(5000, 10000) / 100;

    return getShortName($normalizedFullName) . ' + ' . getShortName($randomPersonFullName) .
        ' = Идеально на ' . number_format($compatibility, 2) . '% ©';
}

// Пример использования функций
foreach ($example_persons_array as $person) {
    $fullname = getFullnameFromParts($person['surname'], $person['name'], $person['patronymic']);
    $parts = getPartsFromFullName($fullname);
    $shortname = getShortName($fullname);
    $gender = getGenderFromName($fullname);

    echo $fullname . "\n"; // Выводит полное имя
    echo $shortname . "\n";
    echo $gender . "\n";
    print_r($parts); // Выводит разбиение на фамилию, имя, отчество
}
echo getGenderDescription($example_persons_array) . "\n";
echo getPerfectPartner('иванов', 'иван', 'иванович', $example_persons_array);
