document.addEventListener("DOMContentLoaded", function() {
    const difficultyButtons = document.querySelectorAll("#difficulty-options button");
    const questionElement = document.getElementById("question");
    const optionsElement = document.getElementById("options");
    const resultElement = document.getElementById("result");
    const timerElement = document.getElementById("time");
    const submitButton = document.getElementById("submit-btn");

    let currentQuestionIndex = 0;
    let score = 0;
    let timeLeft = 600;
    let timerInterval;
    let quizData;

    // const quizDataHard = [
    //     // Hard questions here
    //     {
    //         question: "What is the primary limitation of traditional rule-based expert systems?",
    //         options: ['Inability to handle uncertainty',
    //             'Limited computational power',
    //              'Lack of scalability',
    //              'Difficulty in knowledge representation'],
    //         answer: "Inability to handle uncertainty"
    //
    //     },
    //     {
    //         question: 'Which technique in reinforcement learning aims to estimate the value function directly without explicitly learning a policy?',
    //         options: [ 'Q-Learning',
    //              'Actor-Critic',
    //              'Deep Q-Networks (DQN)',
    //              'Temporal Difference (TD) Learning'],
    //         answer: "Temporal Difference (TD) Learning"
    //     },
    //     {
    //         question: "In natural language processing (NLP), which technique is used to convert words into their base or root form?",
    //         options: ['Tokenization',
    //              'Lemmatization',
    //             'Stemming',
    //              'Part-of-speech tagging'],
    //         answer: "Lemmatization"
    //     },
    //     {
    //         question: "Which neural network architecture is specifically designed to process sequential data, such as time series or natural language?",
    //         options: ['Convolutional Neural Network (CNN)',
    //              'Recurrent Neural Network (RNN)',
    //              'Long Short-Term Memory (LSTM)',
    //              'Generative Adversarial Network (GAN)'],
    //         answer: "Long Short-Term Memory (LSTM)"
    //     },
    //     {
    //         question: "What is the primary objective of the A* algorithm in search and optimization?",
    //         options: ['Minimize the number of explored nodes',
    //              'Maximize the utility function',
    //              'Find the shortest path in a graph',
    //              'Minimize both path cost and heuristic cost'],
    //         answer: "Minimize both path cost and heuristic cost"
    //     },
    //     {
    //         question: "Which of the following is a drawback of genetic algorithms?",
    //         options: [ 'Lack of global optimization capability',
    //              'Susceptibility to premature convergence',
    //              'Limited application in combinatorial optimization problems',
    //              'Inability to handle nonlinear optimization tasks'],
    //         answer: "Susceptibility to premature convergence"
    //     },
    //     {
    //         question: "What is the key difference between supervised learning and unsupervised learning?",
    //         options: [ 'Supervised learning requires labeled data, while unsupervised learning does not.',
    //             'Unsupervised learning requires labeled data, while supervised learning does not.',
    //              'Supervised learning aims at classification tasks, while unsupervised learning focuses on clustering.',
    //              'Unsupervised learning requires feedback from an external agent, while supervised learning does not.'],
    //         answer: "upervised learning requires labeled data, while unsupervised learning does not."
    //     },
    //     {
    //         question: "Which technique is used to mitigate the problem of vanishing gradients in deep neural networks?",
    //         options: [ 'Rectified Linear Unit (ReLU)',
    //             'Gradient clipping',
    //              'Batch normalization',
    //              'Dropout regularization'],
    //         answer: "Batch normalization"
    //     },
    //     {
    //         question: "What is the primary advantage of using ensemble learning methods such as random forests or boosting?",
    //         options: [ 'Improved generalization and robustness',
    //              'Reduced training time',
    //              'Simplified model interpretation',
    //              'Elimination of overfitting'],
    //         answer: " Improved generalization and robustness"
    //     },
    //     {
    //         question: "In Bayesian networks, what does a directed edge between two nodes represent?",
    //         options: [ 'Correlation between variables',
    //              'Causal relationship between variables',
    //              'Mutual exclusivity between variables',
    //              'Conditional independence between variables'],
    //         answer: "Causal relationship between variables"
    //     }
    // ];

    // const quizDataMediumHard = [
    //     // Medium + Hard questions here
    //     {
    //         question: "Which technique is commonly used to pre-process textual data by removing common words that carry little semantic meaning?",
    //         options: ["Stopword removal",
    //          'Stemming',
    //          'Lemmatization',
    //         "Tokenization"],
    //         answer: "Stopword removal"
    //     },
    //     {
    //         question: "In deep learning, what does the term overfitting refer to?",
    //         options: ['The model performs well on the training data but poorly on unseen data.',
    //             'The model fails to converge during training.',
    //              'The model generalizes well to unseen data.',
    //              'The model suffers from high bias.'],
    //         answer: "The model performs well on the training data but poorly on unseen data."
    //     },
    //     {
    //         question: "Which algorithm is used to optimize the weights of a neural network by iteratively updating them based on the gradient of the loss function?",
    //         options: ['Gradient Boosting',
    //             'K-Means',
    //              'Backpropagation',
    //              'Expectation-Maximization (EM)'],
    //         answer: "Backpropagation"
    //     },
    //     {
    //         question: "Which technique is used to transform categorical variables into numerical representations suitable for machine learning algorithms?",
    //         options: ['One-Hot Encoding',
    //              'Principal Component Analysis (PCA)',
    //              'Mean Normalization',
    //              'Feature Scaling'],
    //         answer: "One-Hot Encoding"
    //     },
    //     {
    //         question: "What is the purpose of the activation function in a neural network?",
    //         options: ['To normalize the input data',
    //              'To introduce non-linearity into the network',
    //              'To reduce the dimensionality of the data',
    //             'To regularize the weights of the network'],
    //         answer: "To introduce non-linearity into the network"
    //     },
    //     {
    //         question: "Which approach in machine learning involves mimicking the structure and function of the human brain to build intelligent systems?",
    //         options: ['Reinforcement Learning',
    //              'Supervised Learning',
    //              'Unsupervised Learning',
    //             'Neural Networks'],
    //         answer: "Neural Networks"
    //     },
    //     {
    //         question: "Which theorem provides a formal basis for the Naive Bayes classifier by assuming independence between features?",
    //         options: ['Central Limit Theorem',
    //              'Bayes Theorem',
    //              'Gauss-Markov Theorem',
    //             'Chain Rule'],
    //         answer: "Bayes Theorem"
    //
    //     },
    //     {
    //         question: "Which type of learning technique adjusts the model's parameters based on the difference between predicted and actual outcomes?",
    //         options: ['Supervised Learning',
    //              'Unsupervised Learning',
    //              'Reinforcement Learning',
    //             'Semi-supervised Learning'],
    //         answer: "Supervised Learning"
    //     },
    //     {
    //         question: "What is the process of updating the model's parameters using the gradient of the loss function called in optimization?",
    //         options: ['Backpropagation',
    //              'Forward propagation',
    //              'Gradient descent',
    //             'Stochastic gradient descent'],
    //         answer: "Backpropagation"
    //
    //     },
    //     {
    //         question: "Which evaluation metric is commonly used for classification tasks and represents the ratio of correctly predicted instances to the total instances?",
    //         options: ['Mean Absolute Error (MAE)',
    //              'F1 Score',
    //              'Root Mean Squared Error (RMSE)',
    //             'Accuracy'],
    //         answer: "Accuracy"
    //
    //     }
    //
    //
    // ];

    // const quizDataMedium=[
    //  {
    //      "questions": [
    //        {
    //          "question": "What is the main purpose of the k-nearest neighbors (KNN) algorithm in machine learning?",
    //          "options": {
    //            "A": "Classification",
    //            "B": "Regression",
    //            "C": "Clustering",
    //            "D": "Dimensionality Reduction"
    //          },
    //          "answer": "A) Classification"
    //        },
    //        {
    //          "question": "Which technique is commonly used to address the problem of imbalanced datasets in classification tasks?",
    //          "options": {
    //            "A": "Oversampling",
    //            "B": "Undersampling",
    //            "C": "SMOTE (Synthetic Minority Over-sampling Technique)",
    //            "D": "Random Forest"
    //          },
    //          "answer": "C) SMOTE (Synthetic Minority Over-sampling Technique)"
    //        },
    //        {
    //          "question": "In machine learning, what does the term \"bias\" refer to?",
    //          "options": {
    //            "A": "Error due to fluctuations in data",
    //            "B": "Error due to overly simplistic assumptions in the learning algorithm",
    //            "C": "Error due to high model complexity",
    //            "D": "Error due to noise in the data"
    //          },
    //          "answer": "B) Error due to overly simplistic assumptions in the learning algorithm"
    //        },
    //        {
    //          "question": "Which method is used to select the optimal number of clusters in a K-means clustering algorithm?",
    //          "options": {
    //            "A": "Elbow method",
    //            "B": "Silhouette score",
    //            "C": "Davies–Bouldin index",
    //            "D": "Hopkins statistic"
    //          },
    //          "answer": "A) Elbow method"
    //        },
    //        {
    //          "question": "What is the purpose of regularization techniques such as Lasso and Ridge regression in machine learning?",
    //          "options": {
    //            "A": "To reduce model complexity and prevent overfitting",
    //            "B": "To increase model flexibility and improve generalization",
    //            "C": "To speed up the training process",
    //            "D": "To remove outliers from the dataset"
    //          },
    //          "answer": "A) To reduce model complexity and prevent overfitting"
    //        },
    //        {
    //          "question": "Which algorithm is commonly used for feature selection in machine learning by recursively removing the least important features?",
    //          "options": {
    //            "A": "Principal Component Analysis (PCA)",
    //            "B": "Recursive Feature Elimination (RFE)",
    //            "C": "Random Forest",
    //            "D": "Gradient Boosting"
    //          },
    //          "answer": "B) Recursive Feature Elimination (RFE)"
    //        },
    //        {
    //          "question": "Which method is used to evaluate the performance of a classification model when the classes are imbalanced?",
    //          "options": {
    //            "A": "Precision-Recall Curve",
    //            "B": "Receiver Operating Characteristic (ROC) Curve",
    //            "C": "Mean Squared Error (MSE)",
    //            "D": "F1 Score"
    //          },
    //          "answer": "D) F1 Score"
    //        },
    //        {
    //          "question": "Which technique is used to reduce the dimensionality of data while preserving most of its variance?",
    //          "options": {
    //            "A": "Singular Value Decomposition (SVD)",
    //            "B": "Principal Component Analysis (PCA)",
    //            "C": "Linear Discriminant Analysis (LDA)",
    //            "D": "t-Distributed Stochastic Neighbor Embedding (t-SNE)"
    //          },
    //          "answer": "B) Principal Component Analysis (PCA)"
    //        }
    //      ]
    //    }
    //
    // ];

    // const quizDataEasy = [
    //
    // ];

    function loadQuizData(difficulty) {
        switch (difficulty) {
            case 'hard':
                fetch('quiz_hard.json')
                    .then(response => response.json())
                    .then(data => {
                        quizData = data;
                        displayQuestion();
                        timerInterval = setInterval(countdown, 1000); // Start timer
                        document.getElementById("difficulty-options").style.display = "none";
                    });
                break;
            case 'medium-hard':
                fetch('quiz_medium_hard.json')
                    .then(response => response.json())
                    .then(data => {
                        quizData = data;
                        displayQuestion();
                        timerInterval = setInterval(countdown, 1000);
                        document.getElementById("difficulty-options").style.display = "none";
                    });
                break;
            case 'medium':
                fetch('quiz_medium.json')
                    .then(response => response.json())
                    .then(data => {
                        quizData = data;
                        displayQuestion();
                        timerInterval = setInterval(countdown, 1000);
                        document.getElementById("difficulty-options").style.display = "none";
                    });
                break;
            case 'easy':
                fetch('quiz_easy.json')
                    .then(response => response.json())
                    .then(data => {
                        quizData = data;
                        displayQuestion();
                        timerInterval = setInterval(countdown, 1000);
                        document.getElementById("difficulty-options").style.display = "none";
                    });
                break;
            default:
                // quizData = quizDataMedium;
                break;
        }
    }


    // Function to display question
    function displayQuestion() {
        if (currentQuestionIndex < quizData.length) {
            const currentQuestion = quizData[currentQuestionIndex];
            questionElement.textContent = currentQuestion.question;
            optionsElement.innerHTML = "";
            currentQuestion.options.forEach((option, index) => {
                const button = document.createElement("button");
                button.textContent = option;
                button.addEventListener("click", () => checkAnswer(option));
                optionsElement.appendChild(button);
            });
        } else {
            endQuiz();
        }
    }

    // Function to check answer
    function checkAnswer(selectedAnswer) {
        const currentQuestion = quizData[currentQuestionIndex];
        if (selectedAnswer === currentQuestion.answer) {
            score++;
            resultElement.textContent = "Correct!";
        } else {
            resultElement.textContent = "Wrong!";
        }
        currentQuestionIndex++;
        displayQuestion();
    }

    // Function to end quiz
    function endQuiz() {
        clearInterval(timerInterval);
        resultElement.textContent = `Quiz ended! Your score is ${score}/${quizData.length}`;
        optionsElement.innerHTML = "";
        submitButton.disabled = true;
    }

    // Function for countdown
    function countdown() {
        timerElement.textContent = timeLeft;
        if (timeLeft === 0) {
            endQuiz();
        } else {
            timeLeft--;
        }
    }

    function submitQuiz() {
        checkAnswer();
        endQuiz();
    }

    difficultyButtons.forEach(button => {
        button.addEventListener("click", () => {
            const difficulty = button.id.replace("-btn", "");
            loadQuizData(difficulty);
            displayQuestion();
            timerInterval = setInterval(countdown, 1000);
            document.getElementById("difficulty-options").style.display = "none";
        });
    });

    // Event listener for submit button
    submitButton.addEventListener("click", () => {
        checkAnswer();
    });

});
