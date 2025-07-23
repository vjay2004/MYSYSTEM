// 1. Math Quiz Questions
const questions = [
  {
    question: "What is 5 + 7?",
    options: ["10", "11", "12", "13"],
    answer: "12"
  },
  {
    question: "What is 15 - 6?",
    options: ["8", "9", "10", "11"],
    answer: "9"
  },
  {
    question: "What is 8 × 3?",
    options: ["21", "24", "26", "28"],
    answer: "24"
  },
  {
    question: "What is 20 ÷ 4?",
    options: ["4", "5", "6", "8"],
    answer: "5"
  },
  {
    question: "What is (6 + 2) × 2?",
    options: ["16", "14", "12", "10"],
    answer: "16"
  }
];

let currentQuestion = 0;
let score = 0;

const questionContainer = document.getElementById("question-container");
const optionsContainer = document.getElementById("options-container");
const feedback = document.getElementById("feedback");
const scoreDisplay = document.getElementById("score");
const nextBtn = document.getElementById("next-btn");

// 2. Show a Question
function showQuestion() {
  const q = questions[currentQuestion];
  questionContainer.textContent = q.question;
  optionsContainer.innerHTML = "";
  feedback.textContent = "";

  q.options.forEach(option => {
    const btn = document.createElement("button");
    btn.textContent = option;
    btn.classList.add("option-btn");
    btn.onclick = () => checkAnswer(option);
    optionsContainer.appendChild(btn);
  });
}

// 3. Check the Answer
function checkAnswer(selected) {
  const correct = questions[currentQuestion].answer;
  if (selected === correct) {
    feedback.textContent = "Correct!";
    feedback.style.color = "green";
    score++;
  } else {
    feedback.textContent = `Wrong! Correct answer: ${correct}`;
    feedback.style.color = "red";
  }
  scoreDisplay.textContent = `Score: ${score}`;
  Array.from(optionsContainer.children).forEach(btn => btn.disabled = true);
  nextBtn.style.display = "inline-block";
}

// 4. Move to Next Question
nextBtn.onclick = () => {
  currentQuestion++;
  if (currentQuestion < questions.length) {
    showQuestion();
    nextBtn.style.display = "none";
  } else {
    showFinalScore();
  }
};

// 5. Display Final Score
function showFinalScore() {
  questionContainer.textContent = "Quiz Completed!";
  optionsContainer.innerHTML = "";
  feedback.textContent = score >= questions.length / 2 ? "Great job!" : "You can do better!";
  feedback.style.color = score >= questions.length / 2 ? "green" : "orange";
  scoreDisplay.textContent = `Final Score: ${score}/${questions.length}`;
  nextBtn.style.display = "none";
}

// Start Quiz
showQuestion();
nextBtn.style.display = "none";
