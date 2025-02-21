<?php
// File: index.php
$pageTitle = 'Fitness Tracker - Home';
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
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f2f5;
    }

    .hero-image {
        background-image: url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div id="app"></div>

    <script type="text/babel">
        const Header = () => (
            <header className="bg-white shadow-md">
                <nav className="container mx-auto px-6 py-3">
                    <div className="flex justify-between items-center">
                        <a href="index.php" className="text-2xl font-bold text-gray-800">Fitness Tracker</a>

                    </div>
                </nav>
            </header>
        );

        const Hero = () => (
            <div className="hero-image h-96 flex items-center">
                <div className="container mx-auto px-6">
                    <h1 className="text-4xl font-bold text-white mb-2">Transform Your Body, Transform Your Life</h1>
                    <p className="text-xl text-white mb-8">Track your fitness journey with our comprehensive tools</p>
                    <a href="app/views/auth/login.php" className="bg-blue-500 text-white px-6 py-3 rounded-md text-lg font-semibold hover:bg-blue-600">Get Started</a>
                </div>
            </div>
        );

        const FeatureCard = ({ title, description, icon }) => (
            <div className="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                <div className="text-3xl text-blue-500 mb-4">{icon}</div>
                <h3 className="text-xl font-semibold mb-2">{title}</h3>
                <p className="text-gray-600">{description}</p>
            </div>
        );

        const Features = () => (
            <section className="py-16 bg-gray-100">
                <div className="container mx-auto px-6">
                    <h2 className="text-3xl font-bold text-center text-gray-800 mb-8">Why Choose Us</h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <FeatureCard 
                            title="Personalized Workouts" 
                            description="Get custom workout plans tailored to your fitness goals and experience level."
                            icon="💪"
                        />
                        <FeatureCard 
                            title="Nutrition Tracking" 
                            description="Log your meals and track your macros with our easy-to-use nutrition tools."
                            icon="🥗"
                        />
                        <FeatureCard 
                            title="Progress Monitoring" 
                            description="Visualize your fitness journey with detailed progress charts and analytics."
                            icon="📊"
                        />
                    </div>
                </div>
            </section>
        );

        const Footer = () => (
            <footer className="bg-gray-800 text-white py-8">
                <div className="container mx-auto px-6">
                    <div className="flex flex-wrap justify-between">
                        <div className="w-full md:w-1/4 mb-6 md:mb-0">
                            <h3 className="text-lg font-semibold mb-2">Fitness Tracker</h3>
                            <p className="text-gray-400">Your partner in achieving your fitness goals.</p>
                        </div>
                        <div className="w-full md:w-1/4 mb-6 md:mb-0">
                            <h3 className="text-lg font-semibold mb-2">Quick Links</h3>
                            <ul className="text-gray-400">
                                <li><a href="#" className="hover:text-white">About Us</a></li>
                                <li><a href="#" className="hover:text-white">Contact</a></li>
                                <li><a href="#" className="hover:text-white">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <div className="w-full md:w-1/4 mb-6 md:mb-0">
                            <h3 className="text-lg font-semibold mb-2">Follow Us</h3>
                            <div className="flex space-x-4">
                                <a href="#" className="text-gray-400 hover:text-white">Facebook</a>
                                <a href="#" className="text-gray-400 hover:text-white">Twitter</a>
                                <a href="#" className="text-gray-400 hover:text-white">Instagram</a>
                            </div>
                        </div>
                    </div>
                    <div className="border-t border-gray-700 mt-8 pt-8 text-sm text-gray-400 text-center">
                        &copy; 2025 Fitness Tracker. All rights reserved.
                    </div>
                </div>
            </footer>
        );

        const App = () => (
            <div>
                <Header />
                <Hero />
                <Features />
                <Footer />
            </div>
        );

        ReactDOM.render(<App />, document.getElementById('app'));
    </script>
</body>

</html>