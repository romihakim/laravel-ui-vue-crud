# Membuat CRUD dan Autentikasi dengan Laravel UI Vue
Membuat, membaca, memperbarui, dan menghapus data digunakan hampir setiap aplikasi.
Berikut adalah langkah dalam membuat aplikasi CRUD dan Autentikasi (login & register) menggunakan Laravel (versi 8) dan Vue.

- Step 1 – Download Laravel
- Step 2 – Instal Package laravel/ui
- Step 3 – Membuat Halaman CRUD dengan Vue
- Step 4 – Menambahkan Font Awesome dan TinyMCE

Pastikan sebelumnya sudah menginstal aplikasi Composer dan NPM.

## Step 1 – Download Laravel
Download dan instal Laravel menggunakan Composer, jalankan perintah berikut pada command prompt:
```
D:\Belajar> composer create-project laravel/laravel laravel-ui-vue-crud
```

Setelah instalasi laravel selesai, jangan lupa masuk ke dalam directory project yang sudah dibuat:
```
D:\Belajar> cd laravel-ui-vue-crud
```

Kita sudah bisa menjalankan aplikasi laravel di localhost dengan tampilan default-nya menggunakan perintah Artisan CLI's serve:
```
D:\Belajar\laravel-ui-vue-crud> php artisan serve
```

Untuk menghentikan perintah Artisan CLI's serve, cukup tekan ctrl + c pada command prompt.

Sebelum keproses selanjutnya kita harus menyiapkan database, dan untuk seting koneksinya edit file .env pada directory project.

```
DB_CONNECTION=mysql  
DB_HOST=127.0.0.1  
DB_PORT=3306  
DB_DATABASE=laravel  
DB_USERNAME=root  
DB_PASSWORD=  
```

Jalankan Database Migration untuk men-generate tabel users (default) di database:
```
D:\Belajar\laravel-ui-vue-crud> php artisan migrate
```

Untuk membuat file upload yang dapat diakses dari web, kita harus membuat symbolic link dari *public/storage* ke *storage/app/public*, dengan menggunakan perintah Artisan CLI's storage:link.
```
D:\Belajar\laravel-ui-vue-crud> php artisan storage:link
```


## Step 2 – Instal Package laravel/ui
Laravel UI adalah package yang dikembangkan oleh Laravel untuk menghasilkan kerangka User Interface (UI) dan kode Autentikasi sederhana menggunakan framework CSS Bootstrap.

Jalankan perintah berikut pada command prompt untuk menginstal package laravel/ui:
```
D:\Belajar\laravel-ui-vue-crud> composer require laravel/ui
```

Setelah package laravel/ui terinstal, kita dapat men-generate frontend scaffolding menggunakan perintah **artisan ui**,
jalankan perintah berikut sesuai dengan kebutuhan, apakah aplikasi yang dikembangkan menggunakan sistem autentikasi atau tidak!

```
// Generate login / registration scaffolding...  
D:\Belajar\laravel-ui-vue-crud> php artisan ui vue --auth
  
// Generate basic scaffolding...  
D:\Belajar\laravel-ui-vue-crud> php artisan ui vue
```

Setelah menginstal package laravel/ui dan men-generate frontend scaffolding, kita harus menginstal dependensi Frontend dan JavaScript menggunakan Node Package Manager (NPM):
```
D:\Belajar\laravel-ui-vue-crud> npm install
```

Setelah dependensi terinstal menggunakan **npm install**, compile file SASS (resources/sass/app.scss) dan JavaScript (resources/js/app.js) menggunakan Laravel Mix. 
Perintah **npm run dev** akan memproses instruksi pada file webpack.mix.js, dan hasil kompilasi akan ditempatkan pada directory public/css dan public/js.
```
D:\Belajar\laravel-ui-vue-crud> npm run dev
```

Apabila pada command prompt keluar informasi seperti di bawah ini, kita harus meng-compile ulang file SASS dan JavaScript menggunakan perintah **npm run dev**.
```
Additional dependencies must be installed. This will only take a moment.  
Running: npm install resolve-url-loader@^3.1.2 --save-dev --legacy-peer-deps  
Finished. Please run Mix again.  
```

Sampai di sini package laravel/ui sudah terinstal dan kita bisa menggunakannya. Jalankan aplikasi laravel untuk melihat perubahan dengan menggunakan perintah Artisan CLI's serve.

Untuk lanjut keproses berikutnya, keluar dari aplikasi terlebih dahulu dengan metekan ctrl + c pada command prompt.

## Step 3 – Membuat Halaman CRUD dengan Vue
Dalam membuat aplikasi CRUD di laravel melalui beberapa tahapan, diantaranya:
- Model & Database Migration
- API Authentication
- Resource Controllers
- Setting Routes
- User Interface / Views

### Model & Database Migration

Generate class Model yang ada pada directory *app\Models* dan Database Migration pada directory *database\migrations*, sebagai contoh kita buat tabel **posts** untuk menyimpan data artikel.

Jalankan perintah berikut pada command prompt:
```
D:\Belajar\laravel-ui-vue-crud> php artisan make:model Post -m
```

maka file class **Model** dan **Migration** ter-generate.

Buka file *XXXX_XX_XX_XXXXXX_create_posts_table.php* pada directory Database Migration, edit sesuai dengan kebutuhan:

```
public function up()  
{  
    Schema::create('posts', function (Blueprint $table) {  
        $table->id();  
        $table->integer('category')->unsigned()->default(0);  
        $table->string('title');  
        $table->string('slug')->unique();  
        $table->text('excerpt');  
        $table->text('content');  
        $table->string('image')->nullable();  
        $table->boolean('published')->default(false);  
        $table->integer('user')->unsigned()->default(0);  
        $table->timestamps();  
    });  
}  
```

Menjalankan Database Migration di atas untuk men-generate tabel posts di database:
```
D:\Belajar\laravel-ui-vue-crud> php artisan migrate
```

Selanjutnya buka file *Post.php* pada directory Model, edit sesuai kode berikut:

```
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'published',
    ];
}
```

### API Authentication
Secara default, Laravel memberi solusi sederhana untuk autentikasi API melalui token acak setiap users. 

Sebelum menggunakan driver token, kita perlu menambahkan kolom api_token ke tabel users melalui Database Migration:
```
D:\Belajar\laravel-ui-vue-crud> php artisan make:migration add_token_to_users_table --table=users
```

Buka file *XXXX_XX_XX_XXXXXX_add_token_to_users_table.php* pada directory Database Migration, edit sesuai kode berikut:
```
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('api_token', 80)->after('password')
            ->unique()
            ->nullable()
            ->default(null);
    });
}
```

Jalankan Database Migration di atas menggunakan perintah **php artisan migrate**.

Sekarang kita harus men-generate token setiap user berhasil login. Buka file *app/Http/Controllers/Auth/LoginController.php*, edit sesuai kode berikut:
```
public function __construct()
{
    $this->middleware('guest')->except('logout');
}

protected function authenticated(Request $request, $user)
{
    $token = Str::random(80);

    $request->user()->forceFill([
        'api_token' => $token,
    ])->save();
}
```

### Resource Controllers
Generate class Controller yang ada pada directory *app\Http\Controllers* dengan perintah berikut pada command prompt:

```
D:\Belajar\laravel-ui-vue-crud> php artisan make:controller PostController --resource
```

Selanjutnya buka file *PostController.php* pada directory Controller, edit sesuai kode berikut:

```
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(5);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $posts
        ], 200);
    }

    public function create()
    {
        $token = Auth::user()->api_token;

        return view('post', compact('token'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'mimes:png,jpg,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());
            }

            $req = $request->all();
            $req['slug'] = Str::of($req['slug'] ?? $req['title'])->slug('-');
            $req['excerpt'] = $req['excerpt'] ?? '';
            $req['content'] = $req['content'] ?? '';

            if (isset($image)) {
                $req['image'] = $image->hashName();
            }

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            unset($req['_method']);

            $post = Post::create($req);

            return response()->json([
                'success' => true,
                'message' => 'Post created',
                'data' => $post
            ], 200);
        }
    }

    public function show($id)
    {
        $post = Post::whereId($id)->first();

        if ($post) {
            unset($post->created_at);
            unset($post->updated_at);

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $post
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => '',
                'data' => null
            ], 400);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'mimes:png,jpg,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields',
                'data' => $validator->errors()
            ], 400);
        } else {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());
            }

            $req = $request->all();
            $req['slug'] = Str::of($req['slug'] ?? $req['title'])->slug('-');
            $req['excerpt'] = $req['excerpt'] ?? '';
            $req['content'] = $req['content'] ?? '';

            if (isset($image)) {
                $req['image'] = $image->hashName();
            }

            if (!isset($req['published'])) {
                $req['published'] = 0;
            }

            unset($req['_method']);

            $post = Post::whereId($id)->update($req);

            return response()->json([
                'success' => true,
                'message' => 'Post updated',
                'data' => $post
            ], 200);
        }
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        if ($post) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed'
            ], 400);
        }
    }
}
```

### Setting Routes
Setelah membuat Controller, saatnya mengatur routing web dan api yang telah disediakan laravel.

buka file *routes\web.php*:
```
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/post/{view?}', [App\Http\Controllers\PostController::class, 'create'])->name('post');
    Route::get('/category/{view?}', [App\Http\Controllers\CategoryController::class, 'create'])->name('category');
});
```

buka file *routes\api.php*:
```
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::resource('post', App\Http\Controllers\PostController::class)->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);

    Route::resource('category', App\Http\Controllers\CategoryController::class)->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);
});
```



