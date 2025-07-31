'use strict';

// Elements
const palette = document.getElementById('palette');
const grid = document.getElementById('grid');
const clearBtn = document.getElementById('clear');

// Build a 5x5 grid of boxes
const ROWS = 5, COLS = 5;
for (let r = 0; r < ROWS; r++) {
  for (let c = 0; c < COLS; c++) {
    const cell = document.createElement('div');
    cell.className = 'cell';
    grid.appendChild(cell);
  }
}

// State
let selectedColor = null;

// Pick the first color by default
const firstSwatch = palette.querySelector('.color');
if (firstSwatch) setSelectedColor(firstSwatch.dataset.color, firstSwatch);

// Events

// 1) When a color is clicked, remember it
palette.addEventListener('click', (e) => {
  const swatch = e.target.closest('.color');
  if (!swatch) return;
  setSelectedColor(swatch.dataset.color, swatch);
});

// 2) When a cell is clicked, color its background
grid.addEventListener('click', (e) => {
  const cell = e.target.closest('.cell');
  if (!cell || !selectedColor) return;
  cell.style.backgroundColor = selectedColor;
});

// 3) Clear all cells
clearBtn.addEventListener('click', () => {
  grid.querySelectorAll('.cell').forEach(cell => {
    cell.style.backgroundColor = '';
  });
});

// Helpers
function setSelectedColor(color, swatchEl) {
  selectedColor = color;
  palette.querySelectorAll('.color').forEach(btn => btn.classList.remove('active'));
  if (swatchEl) swatchEl.classList.add('active');
}
