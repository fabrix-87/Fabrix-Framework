# Fabrix Framework

[![License](https://img.shields.io/github/license/fabrix-87/Fabrix-Framework)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)](https://www.php.net/)
[![Composer](https://img.shields.io/badge/composer-%E2%9C%93-success.svg)](https://getcomposer.org/)

Fabrix Framework è un framework PHP creato da Fabrizio Menza. È stato progettato per essere un progetto di studio, seguendo il paradigma MVC (Model-View-Controller) e implementando le raccomandazioni PSR-7, PSR-17 e PSR-18. Fabrix include un gestore delle rotte e utilizza Twig come motore di template per una gestione flessibile delle viste.

## Caratteristiche

- **MVC (Model-View-Controller)**: Organizza la logica di business, le viste e il controllo delle operazioni in maniera modulare.
- **PSR-7, PSR-17, PSR-18**: Conformità agli standard PSR per una gestione robusta delle richieste e risposte HTTP.
- **Gestore delle Routes**: Sistema semplice ed efficace per gestire le rotte dell'applicazione.
- **Twig Template Engine**: Potente motore di template per creare viste dinamiche in modo sicuro e strutturato.

## Requisiti

- **PHP >= 7.4**
- **Composer** per la gestione delle dipendenze

## Installazione

1. Clona il repository:

    ```bash
    git clone https://github.com/fabrix-87/Fabrix-Framework.git
    ```

2. Accedi alla directory del progetto:

    ```bash
    cd Fabrix-Framework
    ```

3. Installa le dipendenze con Composer:

    ```bash
    composer install
    ```

### Configurazione

Puoi configurare il framework modificando il file `app.php` nella directory `Config/`. Questo file include impostazioni per database, mail, e altre configurazioni globali.

### Routing

Le rotte dell'applicazione possono essere definite nel file `routes/web.php`. Ecco un esempio di definizione di una rotta:

```php
Routes::get('home','home@index');
```

### Controller

I controller si trovano nella directory `App/Controllers/`. Ogni controller segue il pattern MVC, gestendo le richieste e rispondendo con una vista o un'operazione.

Esempio di un controller:

```php
namespace App\Controllers;

use System\Core\Controller;

/**
 *
 */
class home_Controller extends Controller
{

    public function index()
    {
        $this->view->show('home');
        return true;
    }
}
```

### Viste

Le viste sono gestite da Twig e si trovano nella directory `Templates/twig/`. Per renderizzare una vista, usa il metodo `show()` all'interno del controller:

```php
$this->view->show('home', ['data' => $data]);
```

### Modelli

I modelli rappresentano la logica di accesso ai dati e si trovano nella directory `App/`. I modelli seguono le convenzioni di naming e gestiscono le operazioni di database.

## Contribuire

Contributi sono benvenuti! Per contribuire:

1. Fai un fork del progetto.
2. Crea un branch per la tua feature (`git checkout -b feature/nuova-feature`).
3. Fai commit delle modifiche (`git commit -m 'Aggiungi nuova feature'`).
4. Fai push del branch (`git push origin feature/nuova-feature`).
5. Apri una Pull Request.

## Licenza

Questo progetto è distribuito sotto licenza MIT. Vedi il file [LICENSE](LICENSE) per maggiori dettagli.

## Contatti

Per domande o supporto, puoi contattare l'autore [Fabrizio Menza](https://github.com/fabrix-87).

```

Questo file `README.md` è stato adattato per il repository su GitHub che hai indicato e riflette accuratamente le informazioni rilevanti per il progetto.