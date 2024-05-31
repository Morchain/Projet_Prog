<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/compaign.css">
    <title>Compaign Mode</title>
</head>
<body>
<img src="../img/campaign.png" alt="Campaign" class="logo">
<a href="game.php" class="back-arrow"><img src="../img/bouton_retour.png" alt="Back"></a>
    <div class="container">
        <div class="arrow" id="left-arrow">&#9664;</div>
        <div class="level-buttons" id="level-buttons">
   
</div>

        <div class="arrow" id="right-arrow">&#9654;</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const leftArrow = document.getElementById('left-arrow');
            const rightArrow = document.getElementById('right-arrow');
            const levelButtons = document.getElementById('level-buttons');

            let currentLevelSet = 1;

            leftArrow.addEventListener('click', () => {
                if (currentLevelSet > 1) {
                    currentLevelSet--;
                    updateLevels();
                }
            });

            rightArrow.addEventListener('click', () => {
                if (currentLevelSet < 2) { 
                    currentLevelSet++;
                    updateLevels();
                }
            });

            function updateLevels() {
    const levels = [];
    for (let i = 1; i <= 4; i++) {
        const levelNumber = (currentLevelSet - 1) * 4 + i;
        levels.push(`<a href='page_for_level_${levelNumber}.html' class='level-link'><img src='../img/level${levelNumber}.png' alt='Level ${levelNumber}' /></a>`);
    }
    levelButtons.innerHTML = levels.join('');
    leftArrow.classList.toggle('disabled', currentLevelSet === 1);
    rightArrow.classList.toggle('disabled', currentLevelSet === 2);
}


            updateLevels(); 
        });
    </script>
</body>
</html>