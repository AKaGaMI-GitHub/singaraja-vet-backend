<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danger!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #0f0f0f;
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .error-code {
            font-size: clamp(100px, 20vw, 200px);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -5px;
            margin-bottom: 2rem;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .eye {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 1em;
            height: 1em;
            background: transparent;
            border-radius: 50%;
            position: relative;
            margin: 0 -0.1em;
        }

        .eye::before {
            content: '';
            position: absolute;
            width: 0.9em;
            height: 0.9em;
            border: 0.1em solid #de2134;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .eye::after {
            content: '';
            position: absolute;
            width: 0.3em;
            height: 0.3em;
            background-color: #de2134;
            border-radius: 50%;
            box-shadow: 0 0 15px #de2134;
            animation: blink 4s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes blink {
            0%, 90%, 100% {
                transform: scale(1);
            }
            95% {
                transform: scale(0.1);
            }
        }

        .message {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 3rem;
        }

        .highlight {
            color: #de2134;
            display: block;
            margin-top: 0.5rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 1.5rem;
            border-radius: 2rem;
            background-color: #de2134;
            color: #0f0f0f;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(222, 33, 52);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: shine 6s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }

        /* Glitch effect */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -10px;
            width: calc(100% + 20px);
            height: 100%;
            background: rgba(210, 255, 0, 0.05);
            animation: glitch 5s infinite;
            clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
            pointer-events: none;
        }

        @keyframes glitch {
            0%, 100% {
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
            }
            2%, 4% {
                clip-path: polygon(0 25%, 100% 25%, 100% 30%, 0 30%);
            }
            6% {
                clip-path: polygon(0 50%, 100% 50%, 100% 55%, 0 55%);
            }
            8% {
                clip-path: polygon(0 70%, 100% 70%, 100% 75%, 0 75%);
            }
            10% {
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }
            
            .message {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">
            4<span class="eye"></span>4
        </div>
        <div class="message">
            We think you're missing out
            <span class="highlight">Nothing to see, here!</span>
        </div>
        <a href="https://www.google.com/" class="btn">Go Back</a>
    </div>
</body>
</html>