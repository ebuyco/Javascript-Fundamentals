```php
<?php
class FileOwners
{
    public static function groupByOwners($files)
    {
        $result=array();
        foreach($files as $key=>$value)
        {
            $result[$value][]=$key;
        }
        return $result;
    }
}

$files = array
(
    "Input.txt" => "Randy",
    "Code.py" => "Stan",
    "Output.txt" => "Randy"
);
print_r(FileOwners::groupByOwners($files));

```