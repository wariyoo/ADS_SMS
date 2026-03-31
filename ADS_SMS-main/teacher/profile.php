<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch teacher details
$stmt = $pdo->prepare("SELECT * FROM TEACHER WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-10 rounded-2xl shadow-xl border border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Teacher Profile
        </h2>

        <?php if ($success): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="profile_process.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" value="<?php echo htmlspecialchars($teacher['name']); ?>" disabled class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                <p class="text-[10px] text-slate-400 mt-1 italic">Name can only be changed by Admin.</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" value="<?php echo htmlspecialchars($teacher['username']); ?>" disabled class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed font-mono">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Change Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Enter new password">
                <p class="text-[10px] text-slate-400 mt-1">Leave blank to keep your current password.</p>
            </div>

            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                <span class="text-[10px] text-indigo-400 block uppercase font-black mb-1">Assigned Grade</span>
                <span class="text-sm font-black text-indigo-900"><?php echo htmlspecialchars($teacher['assigned_class'] ?: 'None'); ?></span>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg">
                Update Profile
            </button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
