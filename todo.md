#TODO


 - __CPD Plugin:__ Plugin that adds the ability to publish and email Certificates of CPD as
   PDF attachments to supervisors. Plugin that adds the ability to publish and email
   Certificated of Appreciation as PDF attachments (See EMS II)
   
    
    
    
    
## NOTES 


HOW TO CALCULATE CPD:

```
/**
 * Calculate the Cpd based on the total number of placement units 
 * @param $totalUnits
 * @return int
 */
static function calculateCpd($totalUnits)
{
    $cpd = ($totalUnits * 5);
    return ($cpd > 20) ? 20 : $cpd;
}

...

$message->set('cpd', \cpd\Db\CpdSetup::calculateCpd($obj->totalUnits));
```






