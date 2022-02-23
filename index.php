<?php

require_once('libs/functions.php');

/**
 * Hàm cho đề a
 * @param $cars - danh sách xe
 * @param array $conditions - danh sách các điều kiện
 * @param int $page - lấy dữ liệu từ trang $page ($page >= 1)
 * @return array
 */
function getCarsWithConditions($cars, $conditions = [], $page = 1)
{

    // lọc theo các tiêu chí
    $cars = array_filter($cars, function ($car) use ($conditions) {

        // loại các xe đã bị xóa
        if ($car->is_delete) {
            return false;
        }

        // tìm tên và thương hiệu chứa từ khóa
        if (
            isset($conditions['search_key']) &&
            (
                !is_numeric(strpos($car->name, $conditions['search_key'])) &&
                !is_numeric(strpos($car->branch, $conditions['search_key']))
            )
        ) {
            return false;
        }

        // tìm theo khoảng từ giá
        if (
            isset($conditions['from_price']) &&
            (double)$conditions['from_price'] > (double)$car->price
        ) {
            return false;
        }

        // tìm theo khoảng đến giá
        if (
            isset($conditions['to_price']) &&
            (double)$conditions['to_price'] < (double)$car->price
        ) {
            return false;
        }

        // tìm theo thương hiệu
        if (isset($conditions['branch'])) {
            $branches = is_array($conditions['branch']) ? $conditions['branch'] : [$conditions['branch']];
            if (!in_array($car->branch, $branches)) {
                return false;
            }
        }

        // tìm theo trạng thái tồn kho
        if (isset($conditions['in_stock']) &&
            (integer)($car->in_stock > 0) !== (integer)($conditions['in_stock'])
        ) {
            return false;
        }

        // tìm theo năm sản xuất
        if (isset($conditions['years'])) {
            $years = is_array($conditions['years']) ? $conditions['years'] : [$conditions['years']];
            if (!in_array($car->year, $years)) {
                return false;
            }
        }

        return true;
    });

    // sắp xếp lại danh sách xe
    $sortingCondition = isset($conditions['sorting_condition']) ? $conditions['sorting_condition'] :
        array(
            "field" => "price", // Tiêu chí sắp xếp
            "sort"  => "asc"
        );
    $fieldSorting = isset($sortingCondition['field']) ? $sortingCondition['field'] : "price";
    $sort = isset($sortingCondition['sort']) ? $sortingCondition['sort'] : "asc";
    usort($cars, function ($car1, $car2) use ($fieldSorting, $sort) {
        return $sort == 'asc' ?
            $car1->$fieldSorting >= $car2->$fieldSorting :
            $car1->$fieldSorting <= $car2->$fieldSorting;
    });


    // phân trang
    $totalCars = count($cars);
    $carsPaginated = array_chunk($cars, 10);

    return array(
        'total_cars'   => $totalCars,
        'total_pages'  => count($carsPaginated),
        'page'         => $page,
        'cars_in_page' => isset($carsPaginated[$page - 1]) ? $carsPaginated[$page - 1] : []
    );

}

/**
 * Hàm cho đề b
 * @param $cars
 * @param array $conditions
 * @return array
 */
function getCarsWithConditionsSpecial($cars, $conditions = [])
{
    // lọc theo các tiêu chí
    $cars = array_filter($cars, function ($car) use ($conditions) {

        // loại các xe đã bị xóa
        if ($car->is_delete) {
            return false;
        }

        // tìm theo thương hiệu
        if (isset($conditions['branch'])) {
            $branches = is_array($conditions['branch']) ? $conditions['branch'] : [$conditions['branch']];
            if (!in_array($car->branch, $branches)) {
                return false;
            }
        }

        // tìm theo trạng thái tồn kho
        if (isset($conditions['in_stock']) &&
            (integer)($car->in_stock > 0) !== (integer)($conditions['in_stock'])
        ) {
            return false;
        }

        // tìm theo năm sản xuất
        if (isset($conditions['years'])) {
            $years = is_array($conditions['years']) ? $conditions['years'] : [$conditions['years']];
            if (!in_array($car->year, $years)) {
                return false;
            }
        }

        return true;
    });

    return array(
        "max_in_stock" => getMaxObjectInArray($cars, "in_stock"),
        "max_price"    => getMaxObjectInArray($cars, "price"),
        "max_year"     => getMaxObjectInArray($cars, "year"),
        "cars"         => $cars
    );

}

function getMaxObjectInArray($array, $field)
{
    $newArray = $array;
    $newArray = array_values($newArray);
    usort($newArray, function ($object1, $object2) use ($field) {
        return $object1->$field < $object2->$field;
    });
    $newArray = array_values($newArray);
    return isset($newArray[0]) ? $newArray[0] : null;
}


// Bắt đầu chạy
$data = file_get_contents('https://kidsonline.edu.vn/test_21012022.json');
$cars1 = json_decode($data);
$cars2 = $cars1;

$carsFiltered1 = getCarsWithConditions($cars2, array(
    'search_key'        => 'Lexus',
    'years'             => [2001, 2002, 2003, 2004],
    'sorting_condition' => [
        "field" => "year",
        "sort"  => "desc"
    ]
));


$carsFiltered2 = getCarsWithConditionsSpecial($cars2, array(
    'branch'        => 'Toyota',
    'sorting_condition' => [
        "field" => "year",
        "sort"  => "asc"
    ]
));

dd($carsFiltered2);