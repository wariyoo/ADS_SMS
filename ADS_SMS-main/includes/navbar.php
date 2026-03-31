<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'User';

if (!$role) return;
?>
<nav class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-indigo-600">ADB-SMS</span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <?php if ($role === 'admin'): ?>
                        <a href="../admin/index.php" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Dashboard</a>
                        <a href="../admin/manage_academic.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Academic</a>
                        <a href="../admin/manage_teachers.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Teachers</a>
                        <a href="../admin/manage_students.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Students</a>
                    <?php elseif ($role === 'teacher'): ?>
                        <a href="../teacher/index.php" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">My Class</a>
                        <a href="../teacher/profile.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Profile</a>
                    <?php elseif ($role === 'student'): ?>
                        <a href="../student/index.php" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">My Report</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <span class="text-sm text-gray-500 mr-4">Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo ucfirst($role); ?>)</span>
                <a href="../auth/logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Logout</a>
            </div>
        </div>
    </div>
</nav>
