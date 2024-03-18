<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>TP INFO802 : Calcul de trajet</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- OpenSstreetMap -->
  <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v6.15.1/build/ol.js"></script>
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
</head>
<body>
    <script></script>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="https://www.univ-smb.fr/">
                <img src="https://www.univ-smb.fr/wp-content/themes/usmb/assets/img/logo.svg" alt="Logo" width="200" height="60" class="d-inline-block align-text-top">
            </a>          
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </nav>

      <script>
        // fetch("https://geo.api.gouv.fr/communes")
        //     .then(response => {
        //         // Gérer la réponse
        //         return response.json(); // ou response.text(), response.blob(), etc.
        //     })
        //     .then(data => {
        //         // Extraire les noms du JSON
        //         const noms = data.map(commune => commune.nom);

        //         // Faire quelque chose avec les noms
        //         console.log(noms);
        //     })
        //     .catch(error => {
        //         // Gérer les erreurs
        //         console.error('Erreur lors de la requête :', error);
        //     });

        function getListeVilles(id){
          fetch("https://geo.api.gouv.fr/communes")
              .then(response => response.json())
              .then(data => {
                  // Extraire les noms du JSON

                  const filtre = data.filter(commune => commune.population > 1500);
                  const noms = filtre.map(commune => commune.nom).sort();
                  // console.log(noms);

                  // Sélectionner l'élément <select>
                  const selectElement = document.getElementById(id);

                  // Ajouter une option vide par défaut
                  const defaultOption = document.createElement("option");
                  defaultOption.text = "";
                  selectElement.add(defaultOption);

                  // Ajouter chaque nom en tant qu'option dans le sélecteur
                  noms.forEach(nom => {
                      const option = document.createElement("option");
                      option.value = nom;
                      option.text = nom;
                      selectElement.add(option);
                      
                  });
              })
              .catch(error => {
                  console.error('Erreur lors de la requête :', error);
              });
          }
          getListeVilles("floatingSelectVille1");
          getListeVilles("floatingSelectVille2");
      </script>

    <div class="d-flex justify-content-around mt-2">
        <div class="mt-2">
            <!-- <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" placeholder="Départ">
                <label for="floatingInput">Départ</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" placeholder="Arrivée">
                <label for="floatingInput">Arrivée</label>
            </div>                -->

            <form method="post" action="accueil.php">
              <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelectVille1" name="depart" aria-label="Floating label select example">
                </select>
                <label for="floatingSelectVille1">Départ</label>
              </div>
              <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelectVille2" name="arrivee" aria-label="Floating label select example">
                </select>
                <label for="floatingSelectVille2">Arrivée</label>
              </div>

              <?php
              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  // Récupérez les valeurs des sélecteurs
                  $depart = $_POST["depart"];
                  $arrivee = $_POST["arrivee"];
              }

              // Bidouillage
              @$ville1 = "https://api-adresse.data.gouv.fr/search/?q=$depart&type=municipality&limit=1";
              $ville1 = '"'.$ville1.'"';
              @$ville2 = "https://api-adresse.data.gouv.fr/search/?q=$arrivee&type=municipality&limit=1";
              $ville2 = '"'.$ville2.'"';
              ?>
              
              <script>
                // console.log("test");
                var ville1 = <?php echo $ville1 ?>;
                // console.log(ville1);
                var ville2 = String(<?php echo $ville2 ?>);
                // console.log(ville2);

                async function getCoord(url, longlat) {
                var long, lat;
                // console.log(`url : ${url}`);

                try {
                  const response = await fetch(url);
                  const data = await response.json();

                  // Supposons que vous voulez récupérer les coordonnées de la première feature
                  if (data.features && data.features.length > 0) {
                    const coordinates = data.features[0].geometry.coordinates;

                    long = coordinates[0]; // Récupérer la coordonnée x
                    lat = coordinates[1]; // Récupérer la coordonnée y
                    // console.log(`long : ${long}`);
                    // console.log(`lat : ${lat}`);

                    // Faites quelque chose avec les coordonnées
                    // console.log(`Url : ${url}, Longitude : ${long}, Latitude : ${lat}`);
                  } else {
                    console.error("Aucune feature trouvée dans les données.");
                  }

                  if (longlat == 0) {
                    // console.log(`long : ${long}`);
                    return long == null ? 48.8566 : long;
                  } else {
                    // console.log(`lat : ${lat}`);
                    return lat == null ? 2.3522 : lat;
                  }
                } catch (error) {
                  console.error("Une erreur s'est produite :", error);
                }
              }

              </script>

            
              <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                  <option selected>Tesla</option>
                  <option value="2">Renaud Zoé</option>
                  <option value="3">Skoda Octavia</option>
                </select>
                <label for="floatingSelect">Véhicules</label>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary m-3" onclick="">Calculer temps de trajet</button>
              </div>
            </form>
            <p>Temps de trajet :</p>
            <div class="alert alert-info d-flex justify-content-around m-2" role="alert">
                55 min
              </div>
          </div>
        <div>
            <div id="map" class="border border-3" style="width: 800px; height: 400px;">
              <script>

                async function initializeMap() {
                  var ville1 = <?php echo $ville1 ?>;
                  var ville2 = <?php echo $ville2 ?>;

                  var long1 = await getCoord(ville1, 0);
                  var lat1 = await getCoord(ville1, 1);
                  var long2 = await getCoord(ville2, 0);
                  var lat2 = await getCoord(ville2, 1);

                  // console.log(`Ville : ${ville1}, Longitude : ${long1}, Latitude : ${lat1}`);
                  // console.log(`Ville : ${ville2}, Longitude : ${long2}, Latitude : ${lat2}`);

                  var map = L.map('map').setView([45.777222, 3.087025], 5);

                  // Ajouter une couche de carte (par exemple, OpenStreetMap)
                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                      attribution: '© OpenStreetMap contributors'
                  }).addTo(map);

                  // Ajouter le contrôle Leaflet Routing Machine
                  var control = L.Routing.control({
                      waypoints: [
                          L.latLng(lat1, long1),  // Coordonnées de départ (Paris)
                          L.latLng(lat2, long2)   // Coordonnées d'arrivée (Berlin)
                      ],
                      routeWhileDragging: true,
                      addWaypoints: false
                  }).addTo(map);

                  // Écouter l'événement "routeselected" pour personnaliser la fenêtre de directions
                  map.on('routeselected', function (e) {
                      var route = e.route;
                      // Ajoutez votre propre logique ici pour personnaliser la fenêtre de directions
                      // console.log("route");
                  });

                  // Écouter l'événement "routeselected" pour personnaliser la fermeture de la fenêtre de directions
                  map.on('routingerror', function (e) {
                      control.setWaypoints([]);
                  });

                  // Ajouter un événement pour fermer la fenêtre de directions
                  map.on('dblclick', function (e) {
                      control.hide();
                  });
                }
                
                initializeMap();

                //Partie GraphQL

                // URL du serveur GraphQL
                const graphqlEndpoint = 'https://api.chargetrip.io/graphql';

                // Exécuter la requête
                fetch(graphqlEndpoint, {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "x-client-id": "5ed1175bad06853b3aa1e492",
                    "x-app-id": "623998b2c35130073829b2d2",
                  },
                  body: JSON.stringify({
                    query: `query vehicleList {
                      vehicleList(
                        page: 0, 
                        size: 20
                      ) {
                        id
                        naming {
                          make
                          model
                          chargetrip_version
                        }
                        media {
                          image {
                            thumbnail_url
                          }
                        }
                        battery {
                          usable_kwh
                        }
                        range {
                          chargetrip_range {
                            best
                            worst
                          }
                        }
                      }
                    }`,
                  }),
                })
                .then(response => response.json())
                .then(data => {
                  if (data.errors) {
                    console.error("Erreurs GraphQL :", data.errors);
                  } else{ 
                  console.log("Réponse GraphQL complète :", data);

                // Vérifiez si vehicleList existe dans la réponse GraphQL
                if (data.data && data.data.vehicleList) {
                  const vehicleList = data.data.vehicleList;

                  // Vérifiez si la liste de véhicules n'est pas nulle
                  if (vehicleList !== null) {
                    vehicleList.forEach(vehicle => {
                      const id = vehicle.id;
                      const make = vehicle.naming.make;
                      const model = vehicle.naming.model;
                      const version = vehicle.naming.chargetrip_version;
                      const thumbnailUrl = vehicle.media.image.thumbnail_url;

                      console.log(`ID: ${id}, Make: ${make}, Model: ${model}, Version: ${version}, Thumbnail URL: ${thumbnailUrl}`);
                    });
                  } else {
                    console.error('La liste de véhicules est nulle dans la réponse GraphQL.');
                  }
                } else {
                  console.error('La propriété vehicleList dans la réponse GraphQL est absente.');
                }
              }
              })
                
              </script>
            </div>
          </div>  
    </div>
</body>
</html>