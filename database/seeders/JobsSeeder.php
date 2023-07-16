<?php

namespace Database\Seeders;

use App\Models\Jobs;
use App\Models\JobsLang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $job = new Jobs();
        $job->active = true;
        $job->publish_date = fake()->dateTimeBetween('+1 month', '+1 year');
        $job->created_at = now();
        $job->updated_at = now();
        if ($job->save()) {
            $job->tags()->attach([5, 6]);
            $jobLang = new JobsLang();
            $jobLang->job_id = $job->id;
            $jobLang->language_id = 1;
            $jobLang->job_title = 'Full Stack Developer';
            $jobLang->job_company = "Wij zijn op zoek naar een getalenteerde Full Stack Developer om ons dynamische team te versterken. Ons bedrijf is een snelgroeiende technologieorganisatie die zich richt op het leveren van innovatieve oplossingen aan klanten over de hele wereld. Als Full Stack Developer speel je een essentiële rol in het ontwerpen, ontwikkelen en implementeren van hoogwaardige softwaretoepassingen.";
            $jobLang->job_description = "Als Full Stack Developer ben je verantwoordelijk voor zowel de front-end als de back-end ontwikkeling van onze softwareprojecten. Je werkt samen met ons team van ontwikkelaars en draagt bij aan de architectuur, het ontwerp en de implementatie van complexe systemen. Je werkt aan diverse taken, zoals het ontwikkelen van gebruiksvriendelijke interfaces, het schrijven van efficiënte code en het integreren van databases en API's. Daarnaast ben je betrokken bij het testen, debuggen en onderhouden van bestaande software.";
            $jobLang->job_qualifications = "<li>Aantoonbare ervaring als Full Stack Developer of vergelijkbare rol</li>
<li>Uitgebreide kennis van HTML, CSS, JavaScript en andere relevante webtechnologieën</li>
<li>Ervaring met frameworks zoals React, Angular of Vue.js</li>
<li>Bekendheid met back-end ontwikkeling en programmeertalen zoals Python, Java, PHP of Node.js</li>
<li>Kennis van databases en SQL</li>
<li>Vaardigheid in het werken met versiebeheersystemen, zoals Git</li>
<li>Probleemoplossend vermogen en oog voor detail</li>
<li>Goede communicatieve vaardigheden en teamgeest</li>
<li>Bereidheid om nieuwe technologieën te leren en jezelf voortdurend te ontwikkelen</li>";
            $jobLang->job_offer = "<li>Een uitdagende functie binnen een innovatieve en groeiende organisatie</li>
<li>De mogelijkheid om met de nieuwste technologieën te werken</li>
<li>Een stimulerende werkomgeving met getalenteerde collega's</li>
<li>Ruimte voor persoonlijke en professionele ontwikkeling</li>
<li>Competitieve salaris- en arbeidsvoorwaarden</li>";
            $jobLang->created_at = now();
            $jobLang->updated_at = now();
            $jobLang->save();

            $jobLang = new JobsLang();
            $jobLang->job_id = $job->id;
            $jobLang->language_id = 2;
            $jobLang->job_title = 'Développeur Full Stack';
            $jobLang->job_company = "Nous recherchons un(e) Développeur(euse) Full Stack talentueux(se) pour renforcer notre équipe dynamique. Notre entreprise est une organisation technologique en pleine croissance qui se concentre sur la fourniture de solutions innovantes à des clients du monde entier. En tant que Développeur(euse) Full Stack, vous jouerez un rôle essentiel dans la conception, le développement et la mise en œuvre d'applications logicielles de haute qualité.";
            $jobLang->job_description = "En tant que Développeur(euse) Full Stack, vous serez responsable du développement à la fois de la partie frontale (front-end) et de la partie arrière (back-end) de nos projets logiciels. Vous travaillerez en collaboration avec notre équipe de développeurs et contribuerez à l'architecture, à la conception et à la mise en œuvre de systèmes complexes. Vous serez chargé(e) de diverses tâches, telles que le développement d'interfaces conviviales, l'écriture de code efficace et l'intégration de bases de données et d'API. De plus, vous participerez aux tests, au débogage et à la maintenance des logiciels existants.";
            $jobLang->job_qualifications = "<li>Avoir une expérience prouvée en tant que Développeur Full Stack ou dans un rôle similaire</li>
  <li>Avoir une connaissance approfondie du HTML, CSS, JavaScript et autres technologies web pertinentes</li>
  <li>Expérience avec des frameworks tels que React, Angular ou Vue.js</li>
  <li>Familiarité avec le développement back-end et les langages de programmation tels que Python, Java, PHP ou Node.js</li>
  <li>Connaissance des bases de données et de SQL</li>
  <li>Compétences en utilisation de systèmes de contrôle de version tels que Git</li>
  <li>Capacité à résoudre des problèmes et souci du détail</li>
  <li>Bonnes compétences en communication et esprit d'équipe</li>
  <li>Volonté d'apprendre de nouvelles technologies et de se développer continuellement</li>";
            $jobLang->job_offer = "<li>Un poste stimulant au sein d'une organisation innovante en pleine croissance</li>
  <li>La possibilité de travailler avec les dernières technologies</li>
  <li>Un environnement de travail stimulant avec des collègues talentueux</li>
  <li>De l'espace pour le développement personnel et professionnel</li>
  <li>Rémunération et avantages compétitifs</li>";
            $jobLang->created_at = now();
            $jobLang->updated_at = now();
            $jobLang->save();

            $jobLang = new JobsLang();
            $jobLang->job_id = $job->id;
            $jobLang->language_id = 3;
            $jobLang->job_title = 'Full Stack Developer';
            $jobLang->job_company = "We are seeking a talented Full Stack Developer to join our dynamic team. Our company is a rapidly growing technology organization that focuses on delivering innovative solutions to clients worldwide. As a Full Stack Developer, you will play an essential role in designing, developing, and implementing high-quality software applications.";
            $jobLang->job_description = "As a Full Stack Developer, you will be responsible for both front-end and back-end development of our software projects. You will collaborate with our team of developers and contribute to the architecture, design, and implementation of complex systems. Your tasks will include developing user-friendly interfaces, writing efficient code, and integrating databases and APIs. Additionally, you will be involved in testing, debugging, and maintaining existing software.";
            $jobLang->job_qualifications = "<li>Demonstrable experience as a Full Stack Developer or similar role</li>
  <li>Extensive knowledge of HTML, CSS, JavaScript, and other relevant web technologies</li>
  <li>Experience with frameworks such as React, Angular, or Vue.js</li>
  <li>Familiarity with back-end development and programming languages like Python, Java, PHP, or Node.js</li>
  <li>Knowledge of databases and SQL</li>
  <li>Proficiency in working with version control systems such as Git</li>
  <li>Problem-solving skills and attention to detail</li>
  <li>Good communication skills and teamwork</li>
  <li>Willingness to learn new technologies and continuously develop yourself</li>";
            $jobLang->job_offer = "<li>A challenging position within an innovative and growing organization</li>
  <li>The opportunity to work with the latest technologies</li>
  <li>A stimulating work environment with talented colleagues</li>
  <li>Room for personal and professional development</li>
  <li>Competitive salary and benefits package</li>";
            $jobLang->created_at = now();
            $jobLang->updated_at = now();
            $jobLang->save();

        }
    }
}
