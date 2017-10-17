# uFiler

This package is offering you a more convenient way to manage file uploads.

It provides the following: 
- an endpoint for uploading files
- a user friendly Vue-component for replacing the regular <input type="file"/>
- helper trait for your Eloquent models to easily create accessors/mutators
- a validation-rule for validating files of your models/entities

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

Note: It's really recommended to submit your entities via javascript instead of native html form submit. However it is not mandatory.

## File-input
You can publish a __file-input.vue__ component into your _/resources/assets/js/components_ folder. Modify the component so it would fit into your design.
```html
<div class="form-group">
    <label for="avatar">Avatar</label>
    <file-input id="avatar" v-model="user.avatar"></file-input>
</div>

```

### Endpoint(Route+Controller)
The package provides an Http endpoint for uploading files. You can disable the endpoint int the configuration file.
If you would like to define your custom endpoint, you can still reuse __UploadControllerTrait__.
```
Method    | URI               | Name          | Action
POST      | upload            |               | Zotyo\uFiler\Http\UploadController@upload
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

### Eloquent
The *HasFile* trait provides methods so you can easily create accessors/mutators in you model.
In the following example, our User model avatar is an file.
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