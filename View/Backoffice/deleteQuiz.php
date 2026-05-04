<?php
include '../../Controller/QuizC.php';  // Inclusion du fichier pour gérer les quiz
$quizC = new QuizC();  // Instanciation de la classe QuizC
$quizC->deleteQuiz($_GET["id"]);  // Appel de la méthode deleteQuiz avec l'identifiant du quiz
header('Location:index.php');  // Redirection vers la liste des quiz
?>
