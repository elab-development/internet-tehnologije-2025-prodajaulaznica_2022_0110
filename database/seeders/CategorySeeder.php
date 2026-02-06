<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Koncert', 'description' => 'Muzički događaji i koncerti'],
            ['name' => 'Sport', 'description' => 'Sportski događaji i utakmice'],
            ['name' => 'Pozorište', 'description' => 'Pozorišne predstave'],
            ['name' => 'Konferencija', 'description' => 'Poslovne konferencije i seminari'],
            ['name' => 'Festival', 'description' => 'Festivali muzike, hrane i kulture'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
