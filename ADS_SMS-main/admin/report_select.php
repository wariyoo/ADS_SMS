<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all available grades
$grades = $pdo->query("SELECT DISTINCT grade FROM STUDENT ORDER BY grade ASC")->fetchAll(PDO::FETCH_COLUMN);

// Fetch all semesters
$semesters = $pdo->query("SELECT s.*, y.year_label FROM SEMESTER s JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id ORDER BY y.year_label DESC, s.semester_label ASC")->fetchAll();
?>

<main class="max-w-xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-10 rounded-2xl shadow-xl border border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Global Academic Report</h2>
        <p class="text-slate-600 mb-8">Select a grade and academic period to generate a full student roster report.</p>

        <form action="generate_report.php" method="GET" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Grade / Section</label>
                <select name="grade" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-slate-50 transition-all font-semibold text-slate-800">
                    <option value="">Select Grade</option>
                    <?php foreach ($grades as $g): ?>
                        <option value="<?php echo htmlspecialchars($g); ?>"><?php echo htmlspecialchars($g); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                Generate Universal Roster
            </button>
        </form>
    </div>

    <!-- Quick Links to other admin areas -->
    <div class="mt-10 grid grid-cols-2 gap-4">
        <a href="manage_students.php" class="bg-white p-4 rounded-xl border border-slate-100 text-center hover:bg-slate-50 transition-all font-bold text-slate-600 text-sm shadow-sm">Manage Students</a>
        <a href="manage_academic.php" class="bg-white p-4 rounded-xl border border-slate-100 text-center hover:bg-slate-50 transition-all font-bold text-slate-600 text-sm shadow-sm">Academic Setup</a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
