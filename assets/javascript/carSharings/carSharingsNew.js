import '../../styles/carSharings/carSharingsNew.css'

  (function(){
    const addVehicleCb = document.querySelector('#cs-form input[type="checkbox"][name$="[addVehicle]"]');
    const quick = document.getElementById('quick-vehicle');
    const vehicleSelect = document.querySelector('#cs-form select[name$="[vehicleId]"]');

    function toggleQuick(){
      const show = addVehicleCb && addVehicleCb.checked;
      quick.style.display = show ? 'block' : 'none';
      if (show && vehicleSelect) vehicleSelect.value = '';
    }

    if (addVehicleCb) {
      addVehicleCb.addEventListener('change', toggleQuick);
      toggleQuick();
    }
  })();