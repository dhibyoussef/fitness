<?php
session_start();
$pageTitle = 'Sign Up - Fitness Tracker';
$errorMessage = '';

if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/react@17.0.2/umd/react.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-dom@17.0.2/umd/react-dom.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/babel-standalone@6.26.0/babel.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div id="app"></div>

    <script type="text/babel">
        const Header = () => (
            <header className="bg-white shadow-md">
                <nav className="container mx-auto px-6 py-3">
                    <div className="flex justify-between items-center">
                        <a href="../../../index.php" className="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                    </div>
                </nav>
            </header>
        );

        const SignUpForm = () => {
            const [name, setName] = React.useState('');
            const [email, setEmail] = React.useState('');
            const [password, setPassword] = React.useState('');
            const [confirmPassword, setConfirmPassword] = React.useState('');

            const handleSubmit = (e) => {
                e.preventDefault();
                // Here you would typically send the sign-up data to your server
                console.log('Sign up submitted:', { name, email, password, confirmPassword });
            };

            return (
                <div className="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-md w-full space-y-8">
                        <div>
                            <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
                        </div>
                        <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                            <input type="hidden" name="remember" value="true" />
                            <div className="rounded-md shadow-sm -space-y-px">
                                <div>
                                    <label htmlFor="name" className="sr-only">Full name</label>
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        required
                                        className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                        placeholder="Full name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="email-address" className="sr-only">Email address</label>
                                    <input
                                        id="email-address"
                                        name="email"
                                        type="email"
                                        autoComplete="email"
                                        required
                                        className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                        placeholder="Email address"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="password" className="sr-only">Password</label>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autoComplete="new-password"
                                        required
                                        className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                        placeholder="Password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="confirm-password" className="sr-only">Confirm password</label>
                                    <input
                                        id="confirm-password"
                                        name="confirm-password"
                                        type="password"
                                        autoComplete="new-password"
                                        required
                                        className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                        placeholder="Confirm password"
                                        value={confirmPassword}
                                        onChange={(e) => setConfirmPassword(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    className="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Sign up
                                </button>
                            </div>
                        </form>
                        <p className="mt-2 text-center text-sm text-gray-600">
                            Already have an account?{' '}
                            <a href="login.php" className="font-medium text-blue-600 hover:text-blue-500">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            );
        };

        const Footer = () => (
            <footer className="bg-gray-800 text-white py-4">
                <div className="container mx-auto px-6 text-center">
                    <p>&copy; 2025 Fitness Tracker. All rights reserved.</p>
                </div>
            </footer>
        );

        const App = () => (
            <div className="flex flex-col min-h-screen">
                <Header />
                <main className="flex-grow">
                    <SignUpForm />
                </main>
                <Footer />
            </div>
        );

        ReactDOM.render(<App />, document.getElementById('app'));
    </script>
</body>

</html>