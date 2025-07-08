<?php
class ProfileController {
    // ... method lainnya
    
    public function showRatings() {
        if (!$this->auth->isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $user = $this->auth->getUser();
        $ratingController = new RatingController();
        $ratings = $ratingController->getRatingsForUser($user['id']);
        $summary = $ratingController->getRatingSummary($user['id']);

        require_once __DIR__ . '/../../templates/header.php';
        require_once __DIR__ . '/../../templates/profile/ratings.php';
        require_once __DIR__ . '/../../templates/footer.php';
    }
}