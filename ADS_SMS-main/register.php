<?php
require_once 'config/db.php';
include 'includes/header.php';

// Fetch Academic Years and Semesters for the registration form
try {
    $years = $pdo->query("SELECT * FROM ACADEMIC_YEAR ORDER BY year_label DESC")->fetchAll();
    $semesters = $pdo->query("SELECT s.*, y.year_label FROM SEMESTER s JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id ORDER BY y.year_label DESC, s.semester_label ASC")->fetchAll();
} catch (PDOException $e) {
    $years = [];
    $semesters = [];
}

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<div class="flex flex-grow items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-slate-100">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-slate-900">
                Student Registration
            </h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Fill in your details to request account approval
            </p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded text-red-700 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 text-sm">
                <?php echo htmlspecialchars($success); ?>
                <div class="mt-2 text-indigo-600 font-medium">
                    <a href="index.php">Back to Login</a>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6" action="auth/register_process.php" method="POST">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input id="name" name="name" type="text" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 placeholder-slate-500 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="John Doe">
            </div>
            
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select id="gender" name="gender" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-slate-50">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input id="username" name="username" type="text" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 placeholder-slate-500 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="johndoe123">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 placeholder-slate-500 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="••••••••">
            </div>

            <div>
                <label for="grade" class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                <input id="grade" name="grade" type="text" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., 9A">
            </div>

            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select id="academic_year" name="academic_year" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-slate-50" onchange="filterSemesters()">
                    <option value="">Select Year</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year['year_id']; ?>"><?php echo htmlspecialchars($year['year_label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select id="semester" name="semester" required class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 text-slate-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-slate-50">
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?php echo $sem['semester_id']; ?>" data-year="<?php echo $sem['year_id']; ?>" class="semester-option"><?php echo htmlspecialchars($sem['semester_label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <script>
                function filterSemesters() {
                    const yearId = document.getElementById('academic_year').value;
                    const semesterSelect = document.getElementById('semester');
                    const options = semesterSelect.getElementsByClassName('semester-option');
                    
                    semesterSelect.value = "";
                    for (let option of options) {
                        if (option.getAttribute('data-year') === yearId || yearId === "") {
                            option.style.display = "";
                        } else {
                            option.style.display = "none";
                        }
                    }
                }
                // Initialize
                filterSemesters();
            </script>

            <div class="md:col-span-2">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                    Submit Registration
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600">
                Already have an account? 
                <a href="index.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                    Log in here
                </a>
            </p>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php';
?>
