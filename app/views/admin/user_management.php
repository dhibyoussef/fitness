<?php

$pageTitle = 'User Management - Fitness Tracker';
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
                        <a href="index.php" className="text-2xl font-bold text-gray-800">Fitness Tracker</a>
                        <div className="space-x-4">
                            <a href="dashboard.php" className="text-gray-600 hover:text-blue-500">Dashboard</a>
                            <a href="user_management.php" className="text-blue-500 font-semibold">User Management</a>
                            <a href="logout.php" className="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Logout</a>
                        </div>
                    </div>
                </nav>
            </header>
        );

        const UserTable = ({ users, onEdit, onDelete }) => (
            <table className="min-w-full bg-white">
                <thead>
                    <tr>
                        <th className="py-2 px-4 border-b">ID</th>
                        <th className="py-2 px-4 border-b">Name</th>
                        <th className="py-2 px-4 border-b">Email</th>
                        <th className="py-2 px-4 border-b">Role</th>
                        <th className="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map(user => (
                        <tr key={user.id}>
                            <td className="py-2 px-4 border-b">{user.id}</td>
                            <td className="py-2 px-4 border-b">{user.name}</td>
                            <td className="py-2 px-4 border-b">{user.email}</td>
                            <td className="py-2 px-4 border-b">{user.role}</td>
                            <td className="py-2 px-4 border-b">
                                <button onClick={() => onEdit(user)} className="text-blue-500 hover:text-blue-700 mr-2">Edit</button>
                                <button onClick={() => onDelete(user.id)} className="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        );

        const UserManagement = () => {
            const [users, setUsers] = React.useState([
                { id: 1, name: 'John Doe', email: 'john@example.com', role: 'user' },
                { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'admin' },
                // Add more mock data as needed
            ]);

            const handleEdit = (user) => {
                // Implement edit functionality
                console.log('Edit user:', user);
            };

            const handleDelete = (userId) => {
                // Implement delete functionality
                setUsers(users.filter(user => user.id !== userId));
            };

            return (
                <div className="container mx-auto px-6 py-8">
                    <h1 className="text-3xl font-bold text-gray-800 mb-8">User Management</h1>
                    <div className="bg-white rounded-lg shadow-md overflow-hidden">
                        <UserTable users={users} onEdit={handleEdit} onDelete={handleDelete} />
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
                    <UserManagement />
                </main>
                <Footer />
            </div>
        );

        ReactDOM.render(<App />, document.getElementById('app'));
    </script>
</body>

</html>