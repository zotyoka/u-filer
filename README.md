# uFiler

The package offers you a more convenient way to manage file uploads in [Laravel](https://laravel.com).

Instead of reinventing the wheel at each file-upload managing your uploaded files will be easy and consistent.

- Upload file with unique name, without concurrency problems.
- Store client-side informations of uploaded files in FileDescriptors.
- File object for ease of managing files in your application.
- Helper trait for your Eloquent models to easily create accessors/mutators.
- User friendly replacement of regular &lt;input type="file"/&gt;

By default the package is configured to upload images but you can use for any mime type.

## Installation

```
composer require "zotyo/u-filer"
```

After downloading the package, add PackageServiceProvider to the providers array of your config/app.php
```
Zotyo\uFiler\PackageServiceProvider::class
```

Finally you should publish config of the package with some examples.
```
php artisan vendor:publish --provider="Zotyo\uFiler\PackageServiceProvider"
```

## Usage
Several components come with the package to reduce the effort of implementing a proper file-upload.

## File-input
You can publish a __file-input.vue__ component into your _/resources/assets/js/components_ folder. Modify the component so it would fit into your design.
```html
<div class="form-group">
    <label for="avatar">Avatar</label>
    <file-input id="avatar" v-model="user.avatar"></file-input>
</div>

```
The vue-component uploads the file to a specific endpoint.

Note: You can avoid [VueJS](https://vuejs.org) at all if you want so.

#### Endpoint(Route+Controller)
The package provides an Http endpoint for uploading files. You can disable the endpoint int the configuration file.
If you would like to define your custom endpoint, you can still reuse __UploadControllerTrait__.
```
Method    | URI               | Name          | Action
POST      | upload            | upload        | Zotyo\uFiler\Http\UploadController@upload
```

### Eloquent
The __HasFile__ trait provides methods so you can easily create accessors/mutators in you model.
In the following example User's avatar is a file.
```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zotyo\uFiler\HasFile;

class User extends Authenticatable
{

    use HasFile;
    protected $fillable = ['avatar'];
    protected $appends  = ['avatar'];

    public function getAvatarAttribute()
    {
        return $this->getFile('avatar');
    }

    public function setAvatarAttribute($value)
    {
        $this->setFile('avatar', $value);
    }
}
```

### Validation
Don't forget to validate files when submitting your entities. The package provides the __verify-file-by-token__ validation rule to avoid hijacking of files.
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'avatar' => 'required|verify-file-by-token'
        ];
    }
}
```