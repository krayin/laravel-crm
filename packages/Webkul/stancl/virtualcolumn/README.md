# Eloquent Virtual Column

## Installation

Supports Laravel 10, 11, and 12.

```
composer require stancl/virtualcolumn
```

## Usage

Use the `VirtualColumn` trait on your model:
```php
use Illuminate\Database\Eloquent\Model;
use Stancl\VirtualColumn\VirtualColumn;

class MyModel extends Model
{
    use VirtualColumn;

    public $guarded = [];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'custom1',
            'custom2',
        ];
    }
}
```

Create a migration:
```php
public function up()
{
    Schema::create('my_models', function (Blueprint $table) {
        $table->increments('id');

        $table->string('custom1')->nullable();
        $table->string('custom2')->nullable();

        $table->json('data');
    });
}
```

And store any data on your model:

```php
$myModel = MyModel::create(['foo' => 'bar']);
$myModel->update(['foo' => 'baz']);
```
