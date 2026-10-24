/**********************************************************/
/*	@copyright	VICTORY group					          */
/*	@support	https://victoryagency.ru/				  */
/**********************************************************/
export function initVictoryMap() {
  const mapsWraps = document.querySelectorAll('[data-map]');
  for (const mapWrap of mapsWraps) {
    if (mapWrap !== undefined && mapWrap !== null) {
      const mapClue = document.createElement('div');
      const mapFrame =
        mapWrap.querySelector('iframe') ??
        mapWrap.querySelector('[data-map-frame]');
      const mapOut = document.createElement('div');
      const mapClueBottom = document.createElement('div');

      mapWrap.style = 'position:relative';
      mapFrame.style = 'pointer-events:none;';

      mapOut.className = 'map-out';
      mapWrap.appendChild(mapOut);

      mapClue.className = 'map-clue';
      mapClue.textContent = 'Для управления картой - нажмите на неё';
      mapOut.appendChild(mapClue);

      if (window.screen.availWidth <= 1200) {
        mapClue.style.cssText = `display:flex;bottom:20px;left:20px;right:20px;`;
      }

      mapClueBottom.className = 'map-clue-bottom';
      mapClueBottom.textContent = 'Выйти из режима просмотра карты';
      document.body.appendChild(mapClueBottom);

      mapOut.addEventListener('click', () => {
        mapFrame.removeAttribute('style');
        if (mapClue !== null) {
          mapClue.classList.toggle('show');
        }

        mapClueBottom.classList.toggle('show');
        mapOut.classList.toggle('hide');
      });

      mapOut.addEventListener('mousemove', (event) => {
        if (window.screen.width > 1200) {
          mapClue.classList.add('show');
          if (event.offsetY > 10) mapClue.style.top = event.offsetY + 20 + 'px';
          if (event.offsetX > 10)
            mapClue.style.left = event.offsetX + 20 + 'px';
        }
      });

      mapWrap.addEventListener('mouseleave', () => {
        mapClue.classList.remove('show');
        mapFrame.style = 'pointer-events:none';
        mapOut.classList.remove('hide');
        mapClueBottom.classList.remove('show');
      });

      mapWrap.addEventListener('touchend', () => {
        mapClue.classList.remove('show');
        mapFrame.style = 'pointer-events:none';
        mapOut.classList.remove('hide');
        mapClueBottom.classList.remove('show');
      });

      mapClueBottom.addEventListener('click', () => {
        mapClueBottom.classList.remove('show');
        mapFrame.style = 'pointer-events:none';
        mapOut.classList.remove('hide');
      });
    }
  }
}
