<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch departments for the form
$departments = $pdo->query("SELECT * FROM DEPARTMENT ORDER BY dept_name ASC")->fetchAll();

// Fetch all teachers
$teachers = $pdo->query("SELECT t.*, d.dept_name FROM TEACHER t LEFT JOIN DEPARTMENT d ON t.department_id = d.department_id ORDER BY t.name ASC")->fetchAll();

$success_msg = $_GET['success'] ?? null;
$new_creds = $_SESSION['new_teacher_creds'] ?? null;
unset($_SESSION['new_teacher_creds']);
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Teacher Management</h1>
            <p class="text-slate-600">Add teachers and assign homeroom classes.</p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
            <?php echo htmlspecialchars($success_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($new_creds): ?>
        <div class="mb-10 bg-indigo-50 border border-indigo-100 p-6 rounded-2xl shadow-sm">
            <h3 class="text-indigo-900 font-bold mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                New Teacher Credentials Generated
            </h3>
            <p class="text-indigo-700 text-sm mb-4">Please provide these to the teacher. They will not be shown again.</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                    <span class="text-xs text-slate-400 block uppercase font-bold">Username</span>
                    <span class="font-mono text-indigo-600"><?php echo htmlspecialchars($new_creds['username']); ?></span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                    <span class="text-xs text-slate-400 block uppercase font-bold">Temporary Password</span>
                    <span class="font-mono text-indigo-600"><?php echo htmlspecialchars($new_creds['password']); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Add Teacher Form -->
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Add New Teacher</h2>
                <form action="teacher_process.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Jane Smith">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-slate-50">
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['dept_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Assigned Grade (Homeroom)</label>
                        <input type="text" name="assigned_class" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="e.g., 9A">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg mt-4">
                        Register & Generate Credentials
                    </button>
                </form>
            </div>
        </div>

        <!-- Teachers List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-sm uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Teacher Info</th>
                            <th class="px-6 py-4 font-semibold">Department</th>
                            <th class="px-6 py-4 font-semibold">Homeroom</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php if (empty($teachers)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">No teachers registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($teachers as $t): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="text-xs text-slate-400">@<?php echo htmlspecialchars($t['username']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold"><?php echo htmlspecialchars($t['dept_name'] ?? 'N/A'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-indigo-600"><?php echo htmlspecialchars($t['assigned_class'] ?? 'None'); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-slate-400 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
