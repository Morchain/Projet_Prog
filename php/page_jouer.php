<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grille PHP à partir de Matrice</title>
    <link rel="stylesheet" href="../css/editeur.css">
    <style>
        table {
            border-collapse: collapse;
        }
        td {
            width: 32px;
            height: 32px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            position: relative;
        }
        .drop-box {
            width: 100%;
            height: 100%;
        }
        .image-container img {
            width: 32px;
            height: 32px;
            position: relative;
        }
        .counter {
            position: absolute;
            top: 0;
            right: 0;
            background-color: red;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            font-size: 12px;
        }
        .trash-container {
            margin-top: 20px;
            border: 2px solid red;
            width: 100px;
            height: 100px;
            text-align: center;
            line-height: 100px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php
        // Définition des images
        $images = [
            1 => "bateau.png",
            2 => "guardianclose.png",
            3 => "krabby.png",
            4 => "pieuvre.png",
            5 => "rock.png",
            6 => "rock1.png",
            7 => "vague.png",
            8 => "nord.png",
            9 => "sud.png",
            10 => "ouest.png",
            11 => "est.png",
            12 => "chest5.png"
        ];

        // Matrice prédéfinie
        $matrice = [
            [1, 0, 4, 0, 0],
            [5, 0, 6, 0, 7],
            [2, 0, 0, 0, 0],
            [5, 3, 4, 0, 0],
            [7, 3, 0, 6, 12]
        ];

        // Initialisation de la direction du bateau
        $directBoat = "est"; 

        function afficherGrille($matrice, $images) {
            echo "<table id='grid'>";
            foreach ($matrice as $ligneIdx => $ligne) {
                echo "<tr>";
                foreach ($ligne as $celluleIdx => $cellule) {
                    echo "<td data-row='$ligneIdx' data-col='$celluleIdx'>";
                    if ($cellule !== 0 && isset($images[$cellule])) {
                        echo '<img src="../pixel_art_projet/32x32/' . $images[$cellule] . '" alt="Image" class="drop-box" data-value="'.$cellule.'">';
                    } else {
                        echo '<div class="drop-box" data-value="0"></div>';
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }

        function afficherMatrice($matrice) {
            echo "<pre id='matrice-display'>";
            foreach ($matrice as $ligne) {
                echo implode(" ", $ligne) . "\n";
            }
            echo "</pre>";
        }

        afficherGrille($matrice, $images);
    ?>

    <div>Direction du bateau : <span id="directionBoat"><?php echo $directBoat; ?></span></div>
    <div>Matrice :</div>
    <?php afficherMatrice($matrice); ?>

    <button id="commencer">Commencer</button>
    <button id="retry" class="hidden">Retry</button>
    <div id="message" class="hidden"></div>

    <div class="image-container">
        <div style="position: relative; display: inline-block;">
            <img src="../pixel_art_projet/32x32/nord.png" draggable="true" id="image8" alt="Nord" data-value="8">
            <div class="counter" id="counter8">2</div>
        </div>
        <div style="position: relative; display: inline-block;">
            <img src="../pixel_art_projet/32x32/sud.png" draggable="true" id="image9" alt="Sud" data-value="9">
            <div class="counter" id="counter9">2</div>
        </div>
        <div style="position: relative; display: inline-block;">
            <img src="../pixel_art_projet/32x32/ouest.png" draggable="true" id="image10" alt="Ouest" data-value="10">
            <div class="counter" id="counter10">2</div>
        </div>
        <div style="position: relative; display: inline-block;">
            <img src="../pixel_art_projet/32x32/est.png" draggable="true" id="image11" alt="Est" data-value="11">
            <div class="counter" id="counter11">2</div>
        </div>
    </div>

    <!-- Section de corbeille -->
    <div class="trash-container">
        Corbeille
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const images = document.querySelectorAll(".image-container img");
            const imageCount = {};
            const maxImages = 2;
            let matrice = <?php echo json_encode($matrice); ?>;
            let directBoat = "<?php echo $directBoat; ?>";
            let interval;

            images.forEach(image => {
                const value = image.getAttribute("data-value");
                imageCount[value] = maxImages;
                image.addEventListener("dragstart", function(event) {
                    event.dataTransfer.setData("text/plain", event.target.id);
                });
            });

            const dropBoxes = document.querySelectorAll(".drop-box");

            dropBoxes.forEach(box => {
                box.addEventListener("dragover", function(event) {
                    event.preventDefault();
                });

                box.addEventListener("drop", function(event) {
                    event.preventDefault();
                    const imageId = event.dataTransfer.getData("text/plain");
                    const value = parseInt(document.getElementById(imageId).getAttribute("data-value"));
                    const row = box.parentElement.getAttribute('data-row');
                    const col = box.parentElement.getAttribute('data-col');

                    if (box.getAttribute("data-value") === "0" && imageCount[value] > 0) {
                        const draggableElement = document.getElementById(imageId).cloneNode(true);
                        draggableElement.addEventListener("dragstart", function(event) {
                            event.dataTransfer.setData("text/plain", event.target.id);
                        });
                        box.innerHTML = ''; // Clear any existing content
                        box.appendChild(draggableElement);
                        box.setAttribute("data-value", value);
                        imageCount[value]--;
                        matrice[row][col] = value;
                        updateMatriceDisplay();
                        updateCounter(value);
                    }
                });
            });

            const trashContainer = document.querySelector(".trash-container");

            trashContainer.addEventListener("dragover", function(event) {
                event.preventDefault();
            });

            trashContainer.addEventListener("drop", function(event) {
                event.preventDefault();
                const imageId = event.dataTransfer.getData("text/plain");
                const draggableElement = document.getElementById(imageId);
                const value = parseInt(draggableElement.getAttribute("data-value"));
                const dropBox = draggableElement.parentElement;
                const row = dropBox.parentElement.getAttribute('data-row');
                const col = dropBox.parentElement.getAttribute('data-col');

                if (dropBox.classList.contains("drop-box")) {
                    dropBox.innerHTML = ''; // Remove the image from the drop box
                    dropBox.setAttribute("data-value", "0");
                    imageCount[value]++;
                    matrice[row][col] = 0;
                    updateMatriceDisplay();
                    updateCounter(value);
                }
            });

            function updateCounter(value) {
                const counter = document.getElementById('counter' + value);
                counter.textContent = imageCount[value];
            }

            function updateMatriceDisplay() {
                const matriceDisplay = document.getElementById('matrice-display');
                matriceDisplay.innerHTML = '';
                matrice.forEach(ligne => {
                    matriceDisplay.innerHTML += ligne.join(" ") + "\n";
                });
            }

            function moveBoat() {
                let boatPosition = findBoatPosition();
                if (!boatPosition) {
                    return;
                }

                let [row, col] = boatPosition;
                matrice[row][col] = 0;
                let nextRow = row, nextCol = col;

                switch (directBoat) {
                    case "nord":
                        nextRow++;
                        break;
                    case "sud":
                        nextRow--;
                        break;
                    case "ouest":
                        nextCol--;
                        break;
                    case "est":
                        nextCol++;
                        break;
                }

                if (nextRow >= 0 && nextRow < matrice.length && nextCol >= 0 && nextCol < matrice[0].length) {
                    let nextCell = matrice[nextRow][nextCol];
                    if (nextCell === 0) {
                        matrice[nextRow][nextCol] = 1;
                    } else if (nextCell === 8) {
                        directBoat = "nord";
                        matrice[nextRow][nextCol] = 1;
                    } else if (nextCell === 9) {
                        directBoat = "sud";
                        matrice[nextRow][nextCol] = 1;
                    } else if (nextCell === 10) {
                        directBoat = "ouest";
                        matrice[nextRow][nextCol] = 1;
                    } else if (nextCell === 11) {
                        directBoat = "est";
                        matrice[nextRow][nextCol] = 1;
                    } else if (nextCell === 12) {
                        clearInterval(interval);
                        document.getElementById('message').textContent = "Partie gagnée!";
                        document.getElementById('message').classList.remove('hidden');
                    } else {
                        matrice[nextRow][nextCol] = 13; // Code pour "boom"
                        clearInterval(interval);
                        document.getElementById('message').textContent = "Game Over";
                        document.getElementById('message').classList.remove('hidden');
                        document.getElementById('commencer').classList.add('hidden');
                        document.getElementById('retry').classList.remove('hidden');
                    }
                } else {
                    clearInterval(interval);
                    document.getElementById('message').textContent = "Game Over";
                    document.getElementById('message').classList.remove('hidden');
                    document.getElementById('commencer').classList.add('hidden');
                    document.getElementById('retry').classList.remove('hidden');
                }

                updateGridDisplay();
                updateMatriceDisplay();
                document.getElementById('directionBoat').textContent = directBoat;
            }

            function findBoatPosition() {
                for (let i = 0; i < matrice.length; i++) {
                    for (let j = 0; j < matrice[i].length; j++) {
                        if (matrice[i][j] === 1) {
                            return [i, j];
                        }
                    }
                }
                return null;
            }

            function updateGridDisplay() {
                const grid = document.getElementById('grid');
                grid.innerHTML = '';
                matrice.forEach((ligne, ligneIdx) => {
                    let tr = document.createElement('tr');
                    ligne.forEach((cellule, celluleIdx) => {
                        let td = document.createElement('td');
                        td.setAttribute('data-row', ligneIdx);
                        td.setAttribute('data-col', celluleIdx);
                        if (cellule !== 0) {
                            let img = document.createElement('img');
                            if (cellule === 13) {
                                img.src = "../pixel_art_projet/32x32/boom.png";
                            } else {
                                img.src = "../pixel_art_projet/32x32/" + <?php echo json_encode($images); ?>[cellule];
                            }
                            img.classList.add('drop-box');
                            img.setAttribute('data-value', cellule);
                            td.appendChild(img);
                        } else {
                            let div = document.createElement('div');
                            div.classList.add('drop-box');
                            div.setAttribute('data-value', '0');
                            td.appendChild(div);
                        }
                        tr.appendChild(td);
                    });
                    grid.appendChild(tr);
                });
            }

            document.getElementById('commencer').addEventListener('click', function() {
                directBoat = "est"; // Initialiser direction à "est" pour démarrer
                interval = setInterval(moveBoat, 1000);
            });

            document.getElementById('retry').addEventListener('click', function() {
                location.reload();
            });
        });
    </script>
</body>
</html>










