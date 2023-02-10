{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_tos_panel'}

  <style>
    .nack-deliveryslot-container {
      min-width: 250px;
      width: 75%;
      margin: 0 auto;
    }

    .nack-date {
      width: 25%;
      height: 10px;
      min-height: 25px;
    }

    .padding {
      width: 25%;
      height: 10px;
      min-height: 25px;
    }

    .nack-header-padding {
      width: 100%;
      height: 20px;
    }

    .nack-header {
      width: 100%;
      padding: 15px;
    }

    .nack-container {
      display: flex;
      flex-wrap: wrap;
    }

    .nack-button {
      margin: 5px;
      width: 20%;
      height: 30px;
      border-radius: 10px;
      background: #e58c7a;
      border-color: unset;
      color: white;
      border-style: hidden;
      cursor: pointer;
      vertical-align: -webkit-baseline-middle;
      display: flex;
      justify-content: space-evenly;
    }

    .nack-input {
      vertical-align: -webkit-baseline-middle;
      align-self: center;
    }

    @media (max-width: 767px) {
      .nack-button {
        width: 60%;
      }

      .nack-date {
        width: 100%;
        display: none;
      }

      .nack-container {
        justify-content: space-around;
      }

      .padding {
        display: none;
      }
    }
  </style>

  <div class="tos--panel panel has--border">
    <div class="panel--title primary is--underline">
      Lieferslots auswählen
    </div>
    <div class="panel--body is--wide">
      <div id="slotheader" class="body--revocation" data-modalbox="true" data-targetselector="a" data-mode="ajax"
        data-height="500" data-width="750">
        Bitte wähle deinen gewünschten Lieferslot für deinen Bezirk : {$postal}
      </div>

      <div class="nack-deliveryslot-container">
        <div id="nack-elements">
        </div>
      </div>
    </div>
  </div>

  <script>
    var data = {$deliverySlots};
    var PLZ = {$postal};

    var count = 0;
    var curDay = new Date(data[0].deliveryDay).getDay();
    var elem = document.createElement('div');
    var options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    var options2 = {
      year: 'numeric',
      month: 'numeric',
      day: 'numeric'
    };
    elem.className = "nack-container";

    data.forEach((obj, index) => {
      if (obj.deliveryAreas.includes(PLZ) && obj.available > 0) {

        if (index === 0) {

          var div = document.createElement("div");
          div.className = "nack-header";
          div.innerText = new Date(data[0].deliveryDay).toLocaleDateString('de-AT', options);
          elem.appendChild(div);

          var padding = document.createElement("div");
          padding.className = "nack-date";
          elem.appendChild(padding);
        }

        if (count == 3) {
          var pad = document.createElement("div");
          pad.className = "padding";
          elem.appendChild(pad);
          count = 0;
        }
        var incomingDay = new Date(obj.deliveryDay).getDay();

        if (curDay != incomingDay) {
          var newContent = document.createElement("div");
          newContent.className = "nack-header";
          newContent.innerText = new Date(obj.deliveryDay).toLocaleDateString('de-AT', options);
          elem.appendChild(newContent);

          var pad3 = document.createElement("div");
          pad3.className = "nack-date";
          elem.appendChild(pad3);
          curDay = incomingDay;
          count = 0;
        }
        var newInput = document.createElement("input");
        var id = "input-" + incomingDay + "-" + obj.slotHours;
        newInput.id = id;
        newInput.className = "nack-input";
        newInput.type = "radio";
        newInput.name = "deliverySlot";
        newInput.value = new Date(obj.deliveryDay).toLocaleDateString('de-AT', options2) + "x" + obj.slotHours;
        newInput.for = "deliverySlot";
        newInput.required = true;
        var labelText = document.createElement("div");
        labelText.innerText = obj.slotHours;
        labelText.className = "nack-input";

        var newLabel = document.createElement("label");
        newLabel.for = id;
        newLabel.className = "nack-button";
        newLabel.appendChild(newInput);
        newLabel.appendChild(labelText);
        elem.appendChild(newLabel);
        count++;
      }
    });

    var final = document.getElementById('nack-elements');
    final.appendChild(elem);
    if (elem.childElementCount == 0) {
      var header = document.getElementById("slotheader");
      header.textContent = "Es stehen für deinen Bezirk (" + PLZ +
        ") leider keine Lieferslots mehr zur Verfügung. Bitte melde dich bei uns im Office unter +43 676 54 18 945.";
      var hiddenInput = document.createElement("input");
      hiddenInput.required = true;
      hiddenInput.name = "hiddenInput";
      hiddenInput.style = "opacity: 0;";
      header.append(hiddenInput);
    }
  </script>

  {$smarty.block.parent}

{/block}