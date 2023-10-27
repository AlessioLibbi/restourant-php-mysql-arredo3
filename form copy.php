<div class="container">
            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="reservation">
                <div class="mb-3 mt-5">
                    <label for="client_name" class="form-label">Nome Cliente</label>
                    <input type="text" class="form-control" name="client_name" id="client_name" aria-describedby="emailHelp">
                    <div id="client_name" class="form-text">Grazie per questa informazione</div>
                </div>
                <div class="mb-5">
                    <label for="prenotation" class="form-label">Data Prenotazione</label>
                    <input type="text" class="form-control" name="prenotation" id="prenotation">
                </div>
                <div class="mb-5">
                    <label for="hour" class="form-label">Orario</label>
                    <input type="number" class="form-control" name="hour" id="hour">
                </div>
                <div class="mb-5">
                    <label for="person_number" class="form-label">Numero Persone</label>
                    <input type="number" class="form-control" id="person_number">
                </div>
            </form>
            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="vote">
                <div class=" form-floating">
                    <textarea class="form-control" placeholder="Leave a comment here" id="vote" style="height: 100px"></textarea>
                    <label for="vote">Lascia un commento</label>
                </div>
                <button type="submit" class="btn mt-3 btn-primary">Submit</button>
            </form>

        </div>