<?php
// test_upload_permissions.php
// Test script to verify upload directory permissions

// 1. Define the test file path
$uploadDir = '/Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/';
$testFile = $uploadDir . 'permission_test.txt';

// 2. Verify directory exists and is writable
if (!file_exists($uploadDir)) {
    die("ERROR: Upload directory doesn't exist at: $uploadDir");
}

if (!is_writable($uploadDir)) {
    die("ERROR: Upload directory is not writable at: $uploadDir");
}

// 3. Attempt to create a test file
$testContent = "This is a test file created at " . date('Y-m-d H:i:s');
if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color: green'>✓ Successfully wrote to: $testFile</p>";
    
    // 4. Verify the file was created
    if (file_exists($testFile)) {
        echo "<p style='color: green'>✓ Test file exists with size: " . filesize($testFile) . " bytes</p>";
        
        // 5. Attempt to delete the test file
        if (unlink($testFile)) {
            echo "<p style='color: green'>✓ Successfully deleted test file</p>";
            echo "<h3>All permission tests passed successfully!</h3>";
        } else {
            echo "<p style='color: red'>✗ Could not delete test file</p>";
        }
    } else {
        echo "<p style='color: red'>✗ Test file was not actually created</p>";
    }
} else {
    echo "<p style='color: red'>✗ Failed to write test file</p>";
    
    // 6. Additional diagnostics
    echo "<h3>Debug Information:</h3>";
    echo "<pre>";
    echo "Directory: $uploadDir\n";
    echo "Exists: " . (file_exists($uploadDir) ? 'Yes' : 'No') . "\n";
    echo "Readable: " . (is_readable($uploadDir) ? 'Yes' : 'No') . "\n";
    echo "Writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "\n";
    
    // Check owner and permissions
    $stat = stat($uploadDir);
    echo "Owner UID: " . $stat['uid'] . "\n";
    echo "Owner GID: " . $stat['gid'] . "\n";
    echo "Permissions: " . decoct($stat['mode'] & 0777) . "\n";
    
    // Check web server user
    echo "Web Server User: " . exec('whoami') . "\n";
    echo "</pre>";
}

// 7. Additional permission recommendations
echo "<h3>Recommended Fixes:</h3>";
echo "<ol>";
echo "<li>Run these commands in terminal:</li>";
echo "<pre>";
echo "sudo chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/\n";
echo "sudo chown -R daemon:daemon /Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/\n";
echo "sudo find /Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/ -type d -exec chmod 755 {} \\;\n";
echo "sudo find /Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/ -type f -exec chmod 644 {} \\;\n";
echo "</pre>";
echo "<li>Make sure XAMPP Apache is running as user 'daemon'</li>";
echo "<li>Check for macOS extended attributes with: <code>sudo xattr -rc /Applications/XAMPP/xamppfiles/htdocs/lagibutuh-website/uploads/</code></li>";
echo "</ol>";
?>