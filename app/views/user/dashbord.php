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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div id="app"></div>

    <script type="text/babel">
        const Header = () => (
            <header className="bg-white shadow-md">
                <nav className="container mx-auto px-6 py-3">
                    <div className="flex justify-between items-center">
                        <a href="index.php" className="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                        <div className="space-x-4">
                            <a href="workout_index.php" className="text-gray-600 hover:text-blue-500">Workouts</a>
                            <a href="nutrition_index.php" className="text-gray-600 hover:text-blue-500">Nutrition</a>
                            <a href="progress_index.php" className="text-gray-600 hover:text-blue-500">Progress</a>
                            <a href="logout.php" className="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                        </div>
                    </div>
                </nav>
            </header>
        );

        const DashboardCard = ({ title, value, icon }) => (
            <div className="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div className="text-3xl text-blue-500 mr-4">{icon}</div>
                <div>
                    <h3 className="text-lg font-semibold text-gray-700">{title}</h3>
                    <p className="text-2xl font-bold text-gray-900">{value}</p>
                </div>
            </div>
        );

        const Dashboard = () => {
            React.useEffect(() => {
                const ctx = document.getElementById('progressChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                        datasets: [{
                            label: 'Weight (kg)',
                            data: [80, 79, 78, 77],
                            borderColor: 'rgb(59, 130, 246)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }, []);

            return (
                <div className="container mx-auto px-6 py-8">
                    <h1 className="text-3xl font-bold text-gray-800 mb-8">Welcome back, User!</h1>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <DashboardCard title="Workouts Completed" value="12" icon="💪" />
                        <DashboardCard title="Calories Burned" value="1,500" icon="🔥" />
                        <DashboardCard title="Weight Loss" value="3 kg" icon="⚖️" />
                    </div>
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">Your Progress</h2>
                        <div className="h-64">
                            <canvas id="progressChart"></canvas>
                        </div>
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
                    <Dashboard />
                </main>
                <Footer />
            </div>
        );

        ReactDOM.render(<App />, document.getElementById('app'));
    </script>
</body>

</html>