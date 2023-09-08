<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
    <link rel="stylesheet" href="<?= $styleAsset ?>">
</head>

<body class="flex flex-col min-h-screen">
    <!-- Header -->
    <header class="p-6 text-center flex-shrink-0">
        <h1 class="text-4xl font-bold">Software Engineer's Blog</h1>
        <nav class="mt-4">
            <ul class="flex justify-center space-x-4">
                <li><a href="#" class="hover-green">Home</a></li>
                <li><a href="#" class="hover-green">About</a></li>
                <li><a href="#" class="hover-green">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-6 flex-grow">
        <article class="bg-white p-6 rounded shadow-lg">
            <h2 class="text-2xl font-bold mb-4">
                <?= $blogTitle ?>
            </h2>
            <p>
                <?= $blogContent ?>
            </p>
            <a href="blog.html" class="text-accent hover:underline mt-2 inline-block">Read More</a>
        </article>

        <!-- Add more articles as needed... -->
    </main>

    <!-- Footer -->
    <footer class="p-6 mt-10 text-center flex-shrink-0" <h3 class="text-xl font-bold mb-4">Stay Updated!</h3>
        <p>Subscribe to get the latest articles and updates.</p>

        <form class="mt-4 flex justify-center space-x-4">
            <input type="email" placeholder="Enter your email" class="p-2 rounded-lg" />
            <button type="submit" class="bg-white text-black px-4 py-2 rounded-lg hover-green">
                Subscribe
            </button>
        </form>

        <div class="flex justify-center items-center mt-6 space-x-6">
            <a href="#" class="text-secondary social-icon hover-green">
                <i class="fab fa-github"></i>
            </a>
            <a href="#" class="text-secondary social-icon hover-green">
                <i class="fab fa-stack-overflow"></i>
            </a>
            <a href="#" class="text-secondary social-icon hover-green">
                <i class="fab fa-dev"></i>
            </a>
        </div>

        <p class="mt-6">
            &copy; 2023 Software Engineer's Blog. All rights reserved.
        </p>
    </footer>
</body>

</html>