<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch semesters where there are subject offerings
try {
    $semesters = $pdo->query("SELECT s.*, y.year_label FROM SEMESTER s JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id ORDER BY y.year_label DESC, s.semester_label ASC")->fetchAll();
} catch (PDOException $e) {
    $semesters = [];
}
?>

<main class="max-w-xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-10 rounded-2xl shadow-xl border border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Generate Class Report</h2>
        <p class="text-slate-600 mb-8">Select the academic period to generate the roster for your homeroom class (<?php echo htmlspecialchars($_SESSION['assigned_class']); ?>).</p>

        <form action="generate_report.php" method="GET" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Academic Period</label>
                <select name="semester_id" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-slate-50 transition-all font-semibold text-slate-800">
                    <option value="">Select Year & Semester</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?php echo $sem['semester_id']; ?>"><?php echo htmlspecialchars($sem['year_label'] . " - " . $sem['semester_label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg hover:shadow-xl flex justify-center items-center">
                Generate Roster Report
            </button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
