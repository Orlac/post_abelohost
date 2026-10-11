<img class="logo" alt="Логотип" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'><rect width='200' height='200' rx='36' fill='%234f46e5'/><path d='M40 70l60 45 60-45v70a10 10 0 0 1-10 10H50a10 10 0 0 1-10-10z' fill='white'/><path d='M40 60a10 10 0 0 1 10-10h100a10 10 0 0 1 10 10l-60 45z' fill='%23c7d2fe'/></svg>">
<h1 class="text-center mb-4">Привет! Приложение запущено 🚀</h1>

{if $categories}
    <div class="row">
        {foreach $categories as $category}
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{$category->title|escape}</h5>
                        <p class="card-text text-muted small">{$category->description|escape}</p>
                        <p class="card-text"><small class="text-muted">ID: {$category->id}</small></p>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <p class="text-muted">Категории отсутствуют.</p>
{/if}