<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    header("Location: index.php");
    exit();
}

// Fetch student details
$stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE student_id = ? AND grade = ?");
$stmt->execute([$student_id, $_SESSION['assigned_class']]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php?error=Student not found or not in your class.");
    exit();
}

// Fetch subjects assigned to this student (Row Existence Principle)
// Join with MARK to see existing grades
try {
    $stmt = $pdo->prepare("SELECT ss.offering_id, s.subject_name, s.total_mark, m.mark 
                          FROM STUDENT_SUBJECT ss
                          JOIN SUBJECT_OFFERING so ON ss.offering_id = so.offering_id
                          JOIN SUBJECT s ON so.subject_id = s.subject_id
                          LEFT JOIN MARK m ON ss.student_id = m.student_id AND ss.offering_id = m.offering_id
                          WHERE ss.student_id = ?");
    $stmt->execute([$student_id]);
    $assignments = $stmt->fetchAll();
} catch (PDOException $e) {
    $assignments = [];
}

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition-colors font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Roster
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Manage Marks - <?php echo htmlspecialchars($student['name']); ?></h1>
            <p class="text-slate-600">ID: <?php echo htmlspecialchars($student['student_id_formatted']); ?> | Grade: <?php echo htmlspecialchars($student['grade']); ?></p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded text-red-700 shadow-sm">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Assigned Subjects</h3>
            <p class="text-sm text-slate-500">Only subjects listed here can have marks entered.</p>
        </div>
        
        <form action="mark_process.php" method="POST" class="p-8">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <div class="space-y-6">
                <?php if (empty($assignments)): ?>
                    <p class="text-center text-slate-400 py-10 italic">No subject assignments found for this student. Please contact Admin.</p>
                <?php else: ?>
                    <?php foreach ($assignments as $subject): ?>
                        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100 hover:border-indigo-200 transition-all group">
                            <div class="mb-4 md:mb-0">
                                <label class="block text-slate-900 font-bold group-hover:text-indigo-600 transition-colors"><?php echo htmlspecialchars($subject['subject_name']); ?></label>
                                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider italic">Max Mark: <?php echo $subject['total_mark']; ?></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="number" 
                                           name="marks[<?php echo $subject['offering_id']; ?>]" 
                                           step="0.01" min="0" max="<?php echo $subject['total_mark']; ?>" 
                                           value="<?php echo $subject['mark']; ?>"
                                           placeholder="Ex: 85"
                                           class="w-24 px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-center font-bold text-slate-800">
                                    <span class="absolute -top-6 left-0 text-[10px] text-slate-400 font-bold">MARK</span>
                                </div>
                                <span class="text-slate-400 font-bold">/ <?php echo $subject['total_mark']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="pt-6 border-t border-slate-100 mt-10">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg hover:shadow-xl flex justify-center items-center">
                            Save All Marks
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
