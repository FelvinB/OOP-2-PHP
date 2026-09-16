<?php
class Student
{
    const BASE_FEE = 2000;

    protected $name;
    protected $studentId;
    protected $course;

    public function __construct($name, $studentId, $course)
    {
        $this->name = $name;
        $this->studentId = $studentId;
        $this->course = $course;
    }

    public function enroll()
    {
        return self::BASE_FEE;
    }

    public function getEnrollmentDetails()
    {
        return 'Name: ' . $this->name . ' | ID: ' . $this->studentId . ' | Course: ' . $this->course;
    }
}

class RegularStudent extends Student
{
    public function enroll()
    {
        return self::BASE_FEE;
    }
}

class ScholarStudent extends Student
{
    public function enroll()
    {
        return self::BASE_FEE * 0.50;
    }
}

class WorkingStudent extends Student
{
    public function enroll()
    {
        return self::BASE_FEE + 500;
    }
}

function getEnrollRule(Student $student)
{
    if ($student instanceof ScholarStudent) {
        return 'Scholarship discount — 50% off the base fee';
    }

    if ($student instanceof WorkingStudent) {
        return 'Working-student rule — base fee plus P500';
    }

    return 'Standard enrollment fee';
}

$types = [
    'regular'  => 'Regular Student',
    'scholar'  => 'Scholar Student',
    'working'  => 'Working Student',
];

$errors = [];
$data = [
    'name'      => '',
    'studentId' => '',
    'course'    => '',
    'type'      => '',
];
$student = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']      = trim($_POST['name'] ?? '');
    $data['studentId'] = trim($_POST['studentId'] ?? '');
    $data['course']    = trim($_POST['course'] ?? '');
    $data['type']      = trim($_POST['type'] ?? '');

    if ($data['name'] === '') {
        $errors[] = 'Student name is required.';
    }

    if ($data['studentId'] === '') {
        $errors[] = 'Student ID is required.';
    }

    if ($data['course'] === '') {
        $errors[] = 'Course is required.';
    }

    if ($data['type'] === '') {
        $errors[] = 'Student type is required.';
    } elseif (!array_key_exists($data['type'], $types)) {
        $errors[] = 'Please select a valid student type.';
    }

    if (empty($errors)) {
        switch ($data['type']) {
            case 'regular':
                $student = new RegularStudent($data['name'], $data['studentId'], $data['course']);
                break;
            case 'scholar':
                $student = new ScholarStudent($data['name'], $data['studentId'], $data['course']);
                break;
            case 'working':
                $student = new WorkingStudent($data['name'], $data['studentId'], $data['course']);
                break;
        }
    }
}

$demoName   = $student ? $data['name'] : 'Juan Dela Cruz';
$demoId     = $student ? $data['studentId'] : '2026-0001';
$demoCourse = $student ? $data['course'] : 'BSIT';

$demoStudents = [
    new RegularStudent($demoName, $demoId, $demoCourse),
    new ScholarStudent($demoName, $demoId, $demoCourse),
    new WorkingStudent($demoName, $demoId, $demoCourse),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment System</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="page">
        <div class="hero">
            <h1>Student Enrollment System</h1>
            <p>PHP OOP — inheritance &amp; polymorphism in action</p>
            <p class="hero-sub">Parent: <code>Student</code> &nbsp;|&nbsp; Children: <code>RegularStudent</code>, <code>ScholarStudent</code>, <code>WorkingStudent</code></p>
        </div>

        <div class="card">
            <h2>Enrollment Form</h2>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="name">Student Name</label>
                    <input type="text" id="name" name="name"
                           value="<?php echo htmlspecialchars($data['name']); ?>"
                           placeholder="e.g. Juan Dela Cruz">
                </div>

                <div class="form-group">
                    <label for="studentId">Student ID</label>
                    <input type="text" id="studentId" name="studentId"
                           value="<?php echo htmlspecialchars($data['studentId']); ?>"
                           placeholder="e.g. 2026-0001">
                </div>

                <div class="form-group">
                    <label for="course">Course</label>
                    <input type="text" id="course" name="course"
                           value="<?php echo htmlspecialchars($data['course']); ?>"
                           placeholder="e.g. BSIT">
                </div>

                <div class="form-group">
                    <label for="type">Student Type</label>
                    <select id="type" name="type">
                        <option value="">-- Select Student Type --</option>
                        <?php foreach ($types as $value => $label): ?>
                            <option value="<?php echo $value; ?>"
                                <?php echo $data['type'] === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn">Submit Enrollment</button>
            </form>
        </div>

        <?php if ($student !== null): ?>
            <div class="card result-card">
                <div class="success-banner">Status: Successfully Enrolled</div>
                <h2>Enrollment Result</h2>
                <div class="result">
                    <p><?php echo htmlspecialchars($student->getEnrollmentDetails()); ?></p>
                    <table>
                        <tr>
                            <td>Student Type:</td>
                            <td><?php echo htmlspecialchars($types[$data['type']]); ?></td>
                        </tr>
                        <tr>
                            <td>Enrollment Rule:</td>
                            <td><?php echo htmlspecialchars(getEnrollRule($student)); ?></td>
                        </tr>
                        <tr>
                            <td>Enrollment Fee:</td>
                            <td>&#8369;<?php echo number_format($student->enroll(), 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Enrollment Demonstration</h2>
            <p class="hint">
                The same <code>enroll()</code> method is called for every object,
                but each subclass provides its own result.
            </p>
            <div class="result-card" style="padding: 0;">
                <table class="demo">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>getEnrollmentDetails()</th>
                            <th>Enrollment Rule</th>
                            <th>enroll() Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demoStudents as $demo): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars(get_class($demo)); ?></code></td>
                                <td><?php echo htmlspecialchars($demo->getEnrollmentDetails()); ?></td>
                                <td><?php echo htmlspecialchars(getEnrollRule($demo)); ?></td>
                                <td>&#8369;<?php echo number_format($demo->enroll(), 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>