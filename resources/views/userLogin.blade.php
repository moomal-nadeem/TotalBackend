<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite('resources/css/app.css')
</head>
<body>
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="px-8 py-6 mt-4 text-left bg-white shadow-lg">
        <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-blue-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path
                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-center">Login to your account</h3>
        <form action="login" method="POST">
            @csrf
            <div class="mt-4">
                <div>
                    <label class="block" for="email">Username<label>
                            <input type="text" name="WebUser" placeholder="Username"
                                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
                                @error('WebUser')
                                <span style="color:red">{{ $message }}</span>
                               @enderror

                </div>
                <div class="mt-4">
                    <label class="block">Password<label>
                            <input type="password" name="WebPass" placeholder="Password"
                                class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
                                @error('WebPass')
                                <span  style="color:red">{{ $message }}</span>
                                @enderror </div>
                <div class="flex items-baseline justify-between">
                    <button type='submit' class="px-6 py-2 mt-4 text-white bg-indigo-600 rounded-lg hover:bg-blue-900">Login</button>
                    <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>