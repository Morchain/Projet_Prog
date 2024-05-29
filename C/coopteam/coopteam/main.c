#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <math.h>
#include "cJSON.h"

// Fonction pour v�rifier si deux positions sont adjacentes
int sontAdjacents(int row1, int col1, int row2, int col2) {
    return abs(row1 - row2) <= 1 && abs(col1 - col2) <= 1;
}

// Fonction pour placer les valeurs 8, 9, 10, 11 adjacentes � la position (row1, col1) du chiffre 1
void placerAdjacents(int** matrix, int size, int row1, int col1, int valeurs[], int nbValeurs) {
    int directions[8][2] = {
        {-1, -1}, {-1, 0}, {-1, 1},
        {0, -1},          {0, 1},
        {1, -1}, {1, 0}, {1, 1}
    };

    for (int i = 0; i < nbValeurs; i++) {
        int placed = 0;
        while (!placed) {
            int dirIndex = rand() % 8;
            int newRow = row1 + directions[dirIndex][0];
            int newCol = col1 + directions[dirIndex][1];

            if (newRow >= 0 && newRow < size && newCol >= 0 && newCol < size && matrix[newRow][newCol] == 0) {
                matrix[newRow][newCol] = valeurs[i];
                placed = 1;
            }
        }
    }
}

int main() {
    int size;
    printf("Choisissez la taille de la grille (entre 5 et 16) : ");
    scanf("%d", &size);

    if (size < 5 || size > 16) {
        printf("Taille invalide.\n");
        return 1;
    }

    // Allocation dynamique de la matrice
    int** matrix = (int**)malloc(size * sizeof(int*));
    for (int i = 0; i < size; i++) {
        matrix[i] = (int*)malloc(size * sizeof(int));
    }

    // Initialiser la matrice avec des z�ros
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size; j++) {
            matrix[i][j] = 0;
        }
    }

    int row1, col1, row2, col2;

    // Initialisation de la graine pour la g�n�ration de nombres al�atoires
    srand((unsigned int)time(NULL));

    // Placer le 1 � une position al�atoire
    row1 = rand() % size;
    col1 = rand() % size;
    matrix[row1][col1] = 1;

    // Placer 8, 9, 10, 11 adjacents � 1
    int valeursAdjacentes[] = { 8, 9, 10, 11 };
    placerAdjacents(matrix, size, row1, col1, valeursAdjacentes, 4);

    // Placer le 12 � une position al�atoire non adjacente
    do {
        row2 = rand() % size;
        col2 = rand() % size;
    } while (sontAdjacents(row1, col1, row2, col2) || (row2 == row1 && col2 == col1) || matrix[row2][col2] != 0);
    matrix[row2][col2] = 12;

    // Calculer le nombre de z�ros � remplacer (40-45% des z�ros restants)
    int totalZeros = size * size - 7; // 7 positions d�j� occup�es (1, 8, 9, 10, 11, 12)
    int minZerosToReplace = (int)(totalZeros * 0.40);
    int maxZerosToReplace = (int)(totalZeros * 0.45);
    int numZerosToReplace = minZerosToReplace + rand() % (maxZerosToReplace - minZerosToReplace + 1);

    int replaced = 0;

    // Remplacer les z�ros par des nombres entre 2 et 7
    while (replaced < numZerosToReplace) {
        int i = rand() % size;
        int j = rand() % size;
        if (matrix[i][j] == 0) {
            int newValue = 2 + rand() % 6; // Nombres entre 2 et 7
            matrix[i][j] = newValue;
            replaced++;
        }
    }

    // Convertir la matrice en JSON
    cJSON* jsonMatrix = cJSON_CreateArray();
    for (int i = 0; i < size; i++) {
        cJSON* row = cJSON_CreateIntArray(matrix[i], size);
        cJSON_AddItemToArray(jsonMatrix, row);
    }

    char* jsonString = cJSON_Print(jsonMatrix);
    printf("%s\n", jsonString);

    // Lib�rer la m�moire allou�e
    for (int i = 0; i < size; i++) {
        free(matrix[i]);
    }
    free(matrix);
    cJSON_Delete(jsonMatrix);
    free(jsonString);

    return 0;
}
