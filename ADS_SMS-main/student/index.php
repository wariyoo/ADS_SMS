<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student details
try {
    $stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching student data.");
}

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>
        <p class="text-slate-600">ID: <?php echo htmlspecialchars($student['student_id_formatted']); ?> | Grade: <?php echo htmlspecialchars($student['grade']); ?></p>
    </div>

    <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Profile Editing (Non-Academic) -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Edit My Profile
            </h2>
            <form action="profile_process.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gender</label>
                    <select name="gender" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-slate-50">
                        <option value="M" <?php echo $student['gender'] === 'M' ? 'selected' : ''; ?>>Male</option>
                        <option value="F" <?php echo $student['gender'] === 'F' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-10">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" placeholder="••••••••">
                </div>
                
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Read-Only Academic Fields</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Grade</span>
                            <span class="text-sm font-bold text-slate-600"><?php echo htmlspecialchars($student['grade']); ?></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase">Student ID</span>
                            <span class="text-sm font-bold text-slate-600"><?php echo htmlspecialchars($student['student_id_formatted']); ?></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Academic Reports -->
        <div class="flex flex-col space-y-6">
            <div class="bg-indigo-600 p-8 rounded-2xl shadow-lg border border-indigo-700 text-white flex-grow">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    My Academic Roster
                </h2>
                <p class="text-indigo-100 text-sm mb-6">Select a semester to view your academic report and performance results.</p>
                
                <form action="view_report.php" method="GET" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">Select Period</label>
                        <?php
                        // Fetch semesters the student is associated with
                        $enrolled_semesters = $pdo->prepare("SELECT DISTINCT s.semester_id, s.semester_label, y.year_label 
                                                            FROM SEMESTER s 
                                                            JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id
                                                            JOIN SUBJECT_OFFERING so ON s.semester_id = so.semester_id
                                                            JOIN STUDENT_SUBJECT ss ON so.offering_id = ss.offering_id
                                                            WHERE ss.student_id = ?
                                                            ORDER BY y.year_label DESC, s.semester_label ASC");
                        $enrolled_semesters->execute([$student_id]);
                        $sems = $enrolled_semesters->fetchAll();
                        ?>
                        <select name="semester_id" required class="w-full px-4 py-3 rounded-xl border border-indigo-500 bg-indigo-700 text-white focus:ring-2 focus:ring-indigo-300 outline-none transition-all">
                            <?php foreach ($sems as $sem): ?>
                                <option value="<?php echo $sem['semester_id']; ?>"><?php echo htmlspecialchars($sem['year_label'] . " - " . $sem['semester_label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-white text-indigo-600 hover:bg-indigo-50 font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                        Generate My Report
                    </button>
                </form>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 mb-2 uppercase tracking-wider">Need Help?</h3>
                <p class="text-xs text-slate-500">Academic fields like Grade and ID are managed by the school Registrar. If you spot an error, please visit the Admin office.</p>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
