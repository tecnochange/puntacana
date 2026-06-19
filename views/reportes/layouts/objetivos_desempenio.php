<style>
  .progress-custom {
    position: relative;
    height: 60px;
    background-color: #f5f5f5;
    border-radius: 6px;
    overflow: hidden;
  }

  .progress-custom .progress-bar {
    height: 100%;
    transition: width 0.4s ease, background-color 0.4s ease;
  }

  .progress-text {
    position: absolute;
    width: 100%;
    text-align: center;
    font-weight: bold;
    font-size: 40px;
    color: #000;
    pointer-events: none;
  }
</style>
<div class="progress progress-custom">
  <div
    class="progress-bar"
    role="progressbar"
    id="progressBar"
    style="width: <?= $valor; ?>%;"
    aria-valuenow="<?= $valor; ?>"
    aria-valuemin="0"
    aria-valuemax="100">
  </div>

  <span class="progress-text"><?= $valor; ?>%</span>
</div>

<div class="mt-3 d-none">
  Evalúa el rendimiento de la compañia en períodos específicos del año, generalmente en intervalos de tres meses.
</div>

<script>
  function setProgress(value) {
    const bar = document.getElementById('progressBar');
    const text = document.querySelector('.progress-text');

    bar.style.width = value + '%';
    bar.setAttribute('aria-valuenow', value);
    text.textContent = value + '%';

    let color = '#00d30a'; // verde

    if (value < 50) {
      color = '#ff0000'; // rojo
    } else if (value < 80) {
      color = '#fff200'; // amarillo
    } else if (value < 96){
      color = '#95fa03'; // verde amarillo  
    } else if (value < 101){
      color = '#0df205'; // verde amarillo  
    } else if (value > 100){
      color = '#00d30a'; // verde   
    }

    bar.style.backgroundColor = color;
  }

  // ejemplo
  setProgress(<?= $valor; ?>);
</script>