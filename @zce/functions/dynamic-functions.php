<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 9/6/2020
 * Time: 3:44 AM
 *
 * @param array $engineer
 */

$engineer = ['Julius', 'Alvarado', 'PHP'];

dataAnalystEngineer(...$engineer);
dataPipelineEngineer(...$engineer);
dataAnalystEngineer(...$engineer);
dataAutomationEngineer(...$engineer);

/*
    OUTPUT:
 - Julius Alvarado will use PHP for dataAnalystEngineer tasks for company

 - Julius Alvarado will use PHP for dataPipelineEngineer tasks for company

 - Julius Alvarado will use PHP for dataAnalystEngineer tasks for company

 - Julius Alvarado will use PHP for dataAutomationEngineer tasks for company
*/

// a few ways to make a function dynamic

function engEcho($first, $last, $tool, $task) {
    $f = "\n - %s %s will use %s for %s tasks at the company\n";
    printf($f, $first, $last, $tool, $task);
}

// scatter operator
function dataPipelineEngineer(...$engineer) {
    $first = $last = $tool = null;
    foreach($engineer as $k => $attr) {
        switch($k) {
            case 0:
                $first = $attr;
                break;
            case 1:
                $last = $attr;
                break;
            case 2:
                $tool = $attr;
                break;
        }
    }
    $m = __FUNCTION__;
    engEcho($first, $last, $tool, $m);
}

// func_get_arg()
function dataAnalystEngineer() {
    $first = func_get_arg(0);
    $last = func_get_arg(1);
    $tool = func_get_arg(2);
    
    $m = __FUNCTION__;
    engEcho($first, $last, $tool, $m);
}

// func_num_args()
function bigDataEngineer() {
    $max = func_num_args();
    $first = $last = $tool = null;
    for($i=0; $i < $max; $i++) {
        if(0===$i) $first = func_get_arg($i);
        else if(1===$i) $last = func_get_arg($i);
        else $tool = func_get_arg($i);
    }
    
    $m = __FUNCTION__;
    engEcho($first, $last, $tool, $m);
}

// func_get_args()
function dataAutomationEngineer() {
    $engineer = func_get_args();
    [$first, $last, $tool] = $engineer;
    
    $m = __FUNCTION__;
    engEcho($first, $last, $tool, $m);
}
