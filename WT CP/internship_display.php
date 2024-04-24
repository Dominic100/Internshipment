<?php
session_start();
include('config.php');

if(isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
}

if(isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];
}
// Process search and filter inputs
$searchTerm = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
$stipendFilter = isset($_POST['stipend']) ? intval($_POST['stipend']) : '';
$locationFilter = isset($_POST['location']) ? $_POST['location'] : '';
$subjectFilter = isset($_POST['subject']) ? $_POST['subject'] : '';

// Prepare the query with search and filters
$query = "SELECT i.*, c.name AS country_name, s.name AS state_name, ci.name AS city_name FROM internshipdata i
          LEFT JOIN countries c ON i.country_id = c.id
          LEFT JOIN states s ON i.state_id = s.id
          LEFT JOIN cities ci ON i.city_id = ci.id";

$conditions = [];
if (!empty($searchTerm)) {
    $conditions[] = "i.title LIKE '%$searchTerm%' OR i.internship_info LIKE '%$searchTerm%'";
}
if (!empty($stipendFilter)) {
    $conditions[] = "i.stipend >= $stipendFilter";
}
if (!empty($locationFilter)) {
    $conditions[] = "(ci.name LIKE '%$locationFilter%' OR s.name LIKE '%$locationFilter%' OR c.name LIKE '%$locationFilter%')";
}
if (!empty($subjectFilter)) {
    $conditions[] = "i.type LIKE '%$subjectFilter%'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY i.id DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0):
    while ($internship = $result->fetch_assoc()):
        ?>
        <div class="internship-card">
            <h3><?php echo htmlspecialchars($internship['title']); ?></h3>
            <hr>
            <p><strong>Company ID:</strong> <?php echo htmlspecialchars($internship['company_id']); ?></p>
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($internship['start_date']); ?></p>
            <p><strong>Duration:</strong> <?php echo htmlspecialchars($internship['duration']); ?> days</p>
            <p><strong>Stipend:</strong> <?php echo htmlspecialchars($internship['stipend']); ?> Rupees</p>
            <p><strong>Subjects:</strong> <?php echo htmlspecialchars($internship['type']); ?></p>
            <p><strong>Location:</strong>
                <?php
                if ($internship['work_from_home'] == 1) {
                    echo "Work From Home";
                } else {
                    // Print city, state, and country information
                    $location = '';
                    if (!empty($internship['city_name'])) {
                        $location .= htmlspecialchars($internship['city_name']);
                    }
                    if (!empty($internship['state_name'])) {
                        $location .= ', ' . htmlspecialchars($internship['state_name']);
                    }
                    if (!empty($internship['country_name'])) {
                        $location .= ', ' . htmlspecialchars($internship['country_name']);
                    }
                    echo $location;
                }
                ?>
            </p>
            <a href="internshipdetails.php?id=<?php echo $internship['id']; ?>&fromInternships=1&company_id=<?php echo $internship['company_id']?>" class="btn btn-info">View Details</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="applyInternship.php?internship_id=<?php echo $internship['id']; ?>&company_id=<?php echo $company_id; ?>" class="btn btn-success ms-2">Apply</a>
            <?php endif; ?>
        </div>
    <?php endwhile;
else: ?>
    <p>No internships found.</p>
<?php endif; ?>