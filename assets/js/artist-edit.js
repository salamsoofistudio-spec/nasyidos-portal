(function () {
    'use strict';

    const bio = document.getElementById('bio');
    const counter = document.getElementById('bio-counter');

    if (!bio || !counter) {
        return;
    }

    const updateCounter = function () {
        const length = bio.value.length;

        counter.textContent =
            length + ' / 5000';
    };

    bio.addEventListener(
        'input',
        updateCounter
    );

    updateCounter();
})();