<?php /**
 * export batch script
 *
 * Use mode:
 *
 * php export.php SERIAL_ID >> serialized_data_SERIAL_ID.xml
 *
 * @package    pumukituvigo
 * @subpackage batch
 * @version    $Id$
 */

define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../..'));
define('SF_APP',         'editar');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       0);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// initialize database manager
$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

// Check input
if (2 != count($argv)) {
    throw new \Exception("\033[31mERROR: Use mode: php export.php SERIAL_ID >> /tmp/export/serial_SERIAL_ID\033[0m");
    exit(-1);
}
if (($id =intval($argv[1])) <= 0) {
    throw new \Exception("\033[31mERROR: Zero or negative ids are not allowed. Please, give a positive id.\033[0m");
    exit(-1);
}

$s = SerialPeer::retrieveByPK($id);

if (is_null($s)){
    throw new \Exception("\033[31mERROR: There is no serial with the given id '".$id."'\033[0m");
    exit(-1);
}

function print_string($string){
    if(strlen($string) == 0) return "";
    return "<![CDATA[" . $string . "]]>";
}

function print_boolean($boolean){
  return ($boolean?"true":"false");
}

?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<?php $langs = sfConfig::get('app_lang_array', array('es')); ?>

<serial>
  <version>0.96</version>
  <id><?php echo $s->getId() ?></id>
  <hash><?php echo SerialHashPeer::get($s)->getHash() ?></hash>
  <title>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </title>
  <subtitle>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getSubTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </subtitle>
  <keyword>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getKeyword()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </keyword>
  <description>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </description>
  <header>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getHeader()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </header>
  <footer>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getFooter()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </footer>
  <line2>
<?php foreach($langs as $lang): $s->setCulture($lang); ?>
    <<?php echo $lang ?>><?php echo print_string($s->getLine2()) ?></<?php echo $lang ?>>
<?php endforeach;?>
  </line2>
  <announce><?php echo print_boolean($s->getAnnounce()) ?></announce>
  <mail><?php echo $s->getMail() ?></mail>
  <copyright><?php echo $s->getCopyright() ?></copyright>
  <publicDate><?php echo $s->getPublicDate() ?></publicDate>
<?php
$c = new Criteria();
$c->add(SerialMatterhornPeer::ID, $s->getId());
if($mh = SerialMatterhornPeer::doSelectOne($c)):
?>
  <opencast>
    <id><?php echo $mh->getMhId()?></id>
  </opencast>
<?php endif ?>
  <serialTemplate id="<?php echo $s->getSerialTemplate()->getId() ?>">
    <name><?php echo $s->getSerialTemplate()->getName() ?></name>
  </serialTemplate>
  <serialType id="<?php if ( $s->getSerialType() !== NULL ) echo $s->getSerialType()->getId() ?>">
    <cod><?php if ( $s->getSerialType() !== NULL ) echo $s->getSerialType()->getCod() ?></cod>
    <defaultsel><?php if ( $s->getSerialType() !== NULL ) echo print_boolean($s->getSerialType()->getDefaultSel()) ?></defaultsel>
    <name>
<?php foreach($langs as $lang): if ( $s->getSerialType() !== NULL ) $s->getSerialType()->setCulture($lang); ?>
      <<?php echo $lang ?>><?php if ( $s->getSerialType() !== NULL ) echo print_string($s->getSerialType()->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
    </name>
    <description>
<?php foreach($langs as $lang): if ( $s->getSerialType() !== NULL ) $s->getSerialType()->setCulture($lang); ?>
      <<?php echo $lang ?>><?php if ( $s->getSerialType() !== NULL ) echo print_string($s->getSerialType()->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
    </description>
  </serialType>
  <serialItuness>
<?php foreach($s->getSerialItuness() as $sit):?>
    <serialItunes id="<?php echo $sit->getId() ?>">
      <itunesId><?php echo $sit->getItunesId() ?></itunesId>
      <culture><?php echo $sit->getCulture() ?></culture>
    </serialItunes>
<?php endforeach;?>
  </serialItuness>
  <pics>
<?php foreach($s->getPicSerials() as $ps):?>
    <pic rank="<?php echo $ps->getRank() ?>" id="<?php echo $ps->getPicId() ?>">
      <url><?php echo print_string($ps->getPic()->getUrl()) ?></url>
    </pic>
<?php endforeach;?>
  </pics>
  <mmTemplates>
<?php foreach($s->getMmTemplates() as $mt):?>
    <mmTemplate rank="<?php echo $mt->getRank() ?>" id="<?php echo $mt->getId() ?>">
      <announce><?php echo print_boolean($mt->getAnnounce()) ?></announce>
      <mail><?php echo $mt->getMail() ?></mail>
      <copyright><?php echo $mt->getCopyright() ?></copyright>
      <recordDate><?php echo $mt->getRecordDate() ?></recordDate>
      <publicDate><?php echo $mt->getPublicDate() ?></publicDate>
      <statusId><?php echo $mt->getStatusId()?></statusId>
      <title>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </title>
      <subtitle>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getSubTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </subtitle>
      <keyword>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getKeyword()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </keyword>
      <description>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </description>
      <subserial><?php echo print_boolean($mt->getSubserial()) ?></subserial>
      <subserialTitle>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getSubserialTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </subserialTitle>
      <line2>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mt->getLine2()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </line2>
      <genre id="<?php echo $mt->getGenre()->getId() ?>">
        <cod><?php echo $mt->getGenre()->getCod() ?></cod>
        <defaultsel><?php echo print_boolean($mt->getGenre()->getDefaultSel()) ?></defaultsel>
        <name>
<?php foreach($langs as $lang): $mt->setCulture($lang); ?>
          <<?php echo $lang ?>><?php echo print_string($mt->getGenre()->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
        </name>
      </genre>
      <broadcast id="<?php echo $mt->getBroadcast()->getId() ?>">
        <name><?php echo $mt->getBroadcast()->getName() ?></name>
        <passwd><?php echo $mt->getBroadcast()->getPasswd() ?></passwd>
        <defaultsel><?php echo print_boolean($mt->getBroadcast()->getDefaultSel()) ?></defaultsel>
<?php $bt=$mt->getBroadcast()->getBroadcastType()?>
        <broadcastType>
          <name><?php echo $bt->getName() ?></name>
          <defaultsel><?php echo print_boolean($bt->getDefaultSel()) ?></defaultsel>
        </broadcastType>
        <description>
<?php foreach($langs as $lang): $mt->getBroadcast()->setCulture($lang); ?>
          <<?php echo $lang ?>><?php echo print_string($mt->getBroadcast()->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
        </description>
      </broadcast>
      <mmTemplatePersons>
<?php foreach(RolePeer::doSelect(new Criteria()) as $role):?>
<?php $mtpersons = $mt->getPersons($role->getId()) ?>
<?php if(count($mtpersons) == 0) continue; ?>
        <role rank="<?php echo $role->getRank() ?>" id="<?php echo $role->getId() ?>">
          <cod><?php echo $role->getCod() ?></cod>
          <xml><?php echo $role->getXml() ?></xml>
          <display><?php echo print_boolean($role->getDisplay()) ?></display>
          <name>
<?php foreach($langs as $lang): $role->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($role->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
          <text>
<?php foreach($langs as $lang): $role->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($role->getText()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </text>
          <persons>
<?php foreach($mtpersons  as $mtperson):?>
            <person id="<?php echo $mtperson->getId() ?>">
              <name><?php echo $mtperson->getName() ?></name>
              <email><?php echo $mtperson->getEmail() ?></email>
              <web><?php echo print_string($mtperson->getWeb()) ?></web>
              <phone><?php echo $mtperson->getPhone() ?></phone>
              <honorific>
<?php foreach($langs as $lang): $mtperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mtperson->getHonorific()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </honorific>
              <firm>
<?php foreach($langs as $lang): $mtperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mtperson->getFirm()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </firm>
              <post>
<?php foreach($langs as $lang): $mtperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mtperson->getPost()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </post>
              <bio>
<?php foreach($langs as $lang): $mtperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mtperson->getBio()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </bio>
            </person>
<?php endforeach;?>
          </persons>
        </role>
<?php endforeach;?>
      </mmTemplatePersons>
      <mmTemplateGrounds>
<?php foreach($mt->getGroundMmTemplates() as $ground_mmt):?>
        <ground rank="<?php echo $ground_mmt->getRank() ?>" id="<?php echo $ground_mmt->getGround()->getId() ?>">
          <cod><?php echo $ground_mmt->getGround()->getCod() ?></cod>
          <name>
<?php foreach($langs as $lang): $ground_mmt->getGround()->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($ground_mmt->getGround()->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
          <groundType id="<?php echo $ground_mmt->getGround()->getGroundType()->getId() ?>">
            <name><?php echo $ground_mmt->getGround()->getgroundType()->getname() ?></name>
            <display><?php echo print_boolean($ground_mmt->getGround()->getgroundType()->getdisplay()) ?></display>
            <rank><?php echo $ground_mmt->getGround()->getgroundType()->getrank() ?></rank>
            <description>
<?php foreach($langs as $lang): $ground_mmt->getGround()->setCulture($lang); ?>
              <<?php echo $lang ?>><?php echo print_string($ground_mmt->getGround()->getGroundType()->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
            </description>
          </groundType>
        </ground>
<?php endforeach;?>
      </mmTemplateGrounds>
    </mmTemplate>
<?php endforeach;?>
  </mmTemplates>
  <mms>
<?php foreach($s->getMms() as $mm):?>
    <mm rank="<?php echo $mm->getRank() ?>" id="<?php echo $mm->getId() ?>">
      <statusId><?php echo $mm->getStatusId() ?></statusId>
      <publicationChannels>
<?php $pubs = PubChannelPeer::doSelect(new Criteria()); foreach($pubs as $p): ?>
        <publicationChannel name="<?php echo $p->getName()?>" enable="<?php echo print_boolean($p->getEnable())?>" status="<?php echo $p->hasMm($mm->getId()) ?>" />
<?php endforeach;?>
      </publicationChannels>
      <publishingDecisions>
<?php if($mm->getAnnounce()):?>
        <publishingDecision name="Announce" />
<?php endif ?>
<?php if($mm->getEditorial1()):?>
        <publishingDecision name="Editorial1" />
<?php endif ?>
<?php if($mm->getEditorial2()):?>
        <publishingDecision name="Editorial2" />
<?php endif ?>
<?php if($mm->getEditorial3()):?>
        <publishingDecision name="Editorial3" />
<?php endif ?>
      </publishingDecisions>
      <announce><?php echo print_boolean($mm->getAnnounce()) ?></announce>
      <mail><?php echo $mm->getMail() ?></mail>
      <copyright><?php echo $mm->getCopyright() ?></copyright>
      <recordDate><?php echo $mm->getRecordDate() ?></recordDate>
      <publicDate><?php echo $mm->getPublicDate() ?></publicDate>
      <title>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </title>
      <subtitle>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getSubTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </subtitle>
      <keyword>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getKeyword()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </keyword>
      <description>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </description>
      <broadcast id="<?php echo $mm->getBroadcast()->getId() ?>">
        <name><?php echo $mm->getBroadcast()->getName() ?></name>
        <passwd><?php echo $mm->getBroadcast()->getPasswd() ?></passwd>
        <defaultsel><?php echo print_boolean($mm->getBroadcast()->getDefaultSel()) ?></defaultsel>
        <broadcastType id="<?php echo $mm->getBroadcast()->getBroadcastTypeId() ?>">
<?php $bt=$mm->getBroadcast()->getBroadcastType()?>
          <name><?php echo $bt->getName() ?></name>
          <defaultsel><?php echo print_boolean($bt->getDefaultSel()) ?></defaultsel>
        </broadcastType>
        <description>
<?php foreach($langs as $lang): $mm->getBroadcast()->setCulture($lang); ?>
          <<?php echo $lang ?>><?php echo print_string($mm->getBroadcast()->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
        </description>
      </broadcast>
      <genre id="<?php echo $mm->getGenre()->getId() ?>">
        <cod><?php echo $mm->getGenre()->getCod() ?></cod>
        <defaultsel><?php echo print_boolean($mm->getGenre()->getDefaultSel()) ?></defaultsel>
        <name>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
          <<?php echo $lang ?>><?php echo print_string($mm->getGenre()->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
        </name>
      </genre>
      <line2>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getLine2()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </line2>
      <subserial><?php echo print_boolean($mm->getSubserial()) ?></subserial>
      <subserialTitle>
<?php foreach($langs as $lang): $mm->setCulture($lang); ?>
        <<?php echo $lang ?>><?php echo print_string($mm->getSubserialTitle()) ?></<?php echo $lang ?>>
<?php endforeach;?>
      </subserialTitle>
      <mmGrounds>
<?php foreach($mm->getGroundMms() as $ground_mm): if(is_null($ground_mm->getGround())) continue;?>
        <ground rank= "<?php echo $ground_mm->getRank() ?>" id="<?php echo $ground_mm->getGround()->getId() ?>">
          <cod><?php echo $ground_mm->getGround()->getCod() ?></cod>
          <name>
<?php foreach($langs as $lang): $ground_mm->getGround()->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($ground_mm->getGround()->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
          <groundType id="<?php echo $ground_mm->getGround()->getGroundType()->getId() ?>">
            <name><?php echo $ground_mm->getGround()->getgroundType()->getname() ?></name>
            <display><?php echo print_boolean($ground_mm->getGround()->getgroundType()->getdisplay()) ?></display>
            <rank><?php echo $ground_mm->getGround()->getgroundType()->getrank() ?></rank>
            <description>
<?php foreach($langs as $lang): $ground_mm->getGround()->setCulture($lang); ?>
              <<?php echo $lang ?>><?php echo print_string($ground_mm->getGround()->getGroundType()->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
            </description>
          </groundType>
          </ground>
<?php endforeach;?>
      </mmGrounds>
      <mmPersons>
<?php foreach(RolePeer::doSelect(new Criteria()) as $role):?>
<?php $mmpersons = $mm->getPersons($role->getId()) ?>
<?php if (count($mmpersons) == 0) continue; ?>
        <role rank="<?php echo $role->getRank() ?>" id="<?php echo $role->getId() ?>">
          <cod><?php echo $role->getCod() ?></cod>
          <xml><?php echo $role->getXml() ?></xml>
          <display><?php echo print_boolean($role->getDisplay()) ?></display>
          <name>
<?php foreach($langs as $lang): $role->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($role->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
          <text>
<?php foreach($langs as $lang): $role->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($role->getText()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </text>
          <persons>
<?php foreach($mmpersons as $mmperson):?>
            <person id="<?php echo $mmperson->getId() ?>">
              <name><?php echo $mmperson->getName() ?></name>
              <email><?php echo $mmperson->getEmail() ?></email>
              <web><?php echo print_string($mmperson->getWeb()) ?></web>
              <phone><?php echo $mmperson->getPhone() ?></phone>
              <honorific>
<?php foreach($langs as $lang): $mmperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mmperson->getHonorific()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </honorific>
              <firm>
<?php foreach($langs as $lang): $mmperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mmperson->getFirm()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </firm>
              <post>
<?php foreach($langs as $lang): $mmperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mmperson->getPost()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </post>
              <bio>
<?php foreach($langs as $lang): $mmperson->setCulture($lang); ?>
                <<?php echo $lang ?>><?php echo print_string($mmperson->getBio()) ?></<?php echo $lang ?>>
<?php endforeach;?>
              </bio>
            </person>
<?php endforeach;?>
          </persons>
        </role>
<?php endforeach;?>
      </mmPersons>
      <mmPics>
<?php foreach($mm->getPicMms() as $picmm): if (is_null($picmm->getPic())) continue;?>
        <pic rank="<?php echo $picmm->getRank() ?>" id="<?php echo $picmm->getPic()->getId() ?>">
          <url><?php echo print_string($picmm->getPic()->getUrl()) ?></url>
        </pic>
<?php endforeach;?>
      </mmPics>
      <files>
<?php foreach($mm->getFiles() as $file):?>
        <file rank="<?php echo $file->getRank() ?>" id="<?php echo $file->getId() ?>">
          <file><?php echo print_string($file->getFile()) ?></file>
          <description>
<?php foreach($langs as $lang): $file->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($file->getDescription()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </description>
          <perfil id="<?php echo $file->getPerfilId() ?>">
            <name><?php echo $file->getPerfil()->getName() ?></name>
          </perfil>
          <url><?php echo print_string($file->getUrl()) ?></url>
          <format id="<?php echo $file->getFormatId() ?>">
            <name><?php if ( $file->getFormat() !== NULL ) echo $file->getFormat()->getName() ?></name>
            <defaultsel><?php if ( $file->getFormat() !== NULL ) echo print_boolean($file->getFormat()->getDefaultsel()) ?></defaultsel>
          </format>
          <codec id="<?php echo $file->getCodecId() ?>">
            <name><?php if ( $file->getCodec() !== NULL ) echo $file->getCodec()->getName() ?></name>
            <defaultsel><?php if ( $file->getCodec() !== NULL ) echo print_boolean($file->getCodec()->getDefaultsel()) ?></defaultsel>
          </codec>
          <mimetype id="<?php echo $file->getMimeTypeId() ?>">
            <name><?php if ( $file->getMimeType() !== NULL ) echo $file->getMimeType()->getName() ?></name>
            <defaultsel><?php if ( $file->getMimeType() !== NULL ) echo print_boolean($file->getMimeType()->getDefaultsel()) ?></defaultsel>
            <type><?php if ( $file->getMimeType() !== NULL ) echo $file->getMimeType()->getType() ?></type>
          </mimetype>
          <resolution id="<?php echo $file->getMimeTypeId() ?>">
            <hor><?php if ( $file->getResolution() !== NULL ) echo $file->getResolution()->getHor() ?></hor>
            <defaultsel><?php if ( $file->getResolution() !== NULL ) echo print_boolean($file->getResolution()->getDefaultsel()) ?></defaultsel>
            <ver><?php if ( $file->getResolution() !== NULL ) echo $file->getResolution()->getVer() ?></ver>
          </resolution>
          <bitrate><?php echo $file->getBitrate() ?></bitrate>
          <framerate><?php echo $file->getFramerate() ?></framerate>
          <channels><?php echo $file->getChannels() ?></channels>
          <audio><?php echo $file->getAudio() ?></audio>
          <duration><?php echo $file->getDuration() ?></duration>
          <numview><?php echo $file->getNumView() ?></numview>
          <puntsum><?php echo $file->getPuntSum() ?></puntsum>
          <puntnum><?php echo $file->getPuntNum() ?></puntnum>
          <size><?php echo $file->getSize() ?></size>
          <resolutionhor><?php echo $file->getResolutionHor() ?></resolutionhor>
          <resolutionver><?php echo $file->getResolutionVer() ?></resolutionver>
          <display><?php echo print_boolean($file->getDisplay()) ?></display>
          <language>
            <cod><?php echo $file->getLanguage()->getCod() ?></cod>
            <defaultsel><?php echo print_boolean($file->getLanguage()->getDefaultsel()) ?></defaultsel>
            <name>
<?php foreach($langs as $lang): $file->setCulture($lang); ?>
              <<?php echo $lang ?>><?php echo print_string($file->getLanguage()) ?></<?php echo $lang ?>>
<?php endforeach;?>
            </name>
          </language>
          <tickets>
<?php foreach($file->getTickets() as $ticket):?>
            <ticket id="<?php echo $ticket->getId() ?>">
              <FileId><?php echo $ticket->getFileId() ?></FileId>
              <path><?php echo $ticket->getPath() ?></path>
              <url><?php echo print_string($ticket->getUrl()) ?></url>
              <date><?php echo $ticket->getDate() ?></date>
              <end><?php echo $ticket->getEnd() ?></end>
            </ticket>
<?php endforeach;?>
          </tickets>
        </file>
<?php endforeach;?>
      </files>
      <materials>
<?php foreach($mm->getMaterials() as $mat):?>
        <material rank="<?php echo $mat->getRank() ?>" id="<?php echo $mat->getId() ?>">
          <name>
<?php foreach($langs as $lang): $mat->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($mat->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
          <url><?php echo print_string($mat->getUrl()) ?></url>
          <display><?php echo print_boolean($mat->getDisplay()) ?></display>
          <mattype id="<?php echo $mat->getMatType()->getId() ?>">
            <name>
<?php foreach($langs as $lang): $mat->setCulture($lang); ?>
              <<?php echo $lang ?>><?php echo print_string($mat->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
            </name>
            <type><?php echo $mat->getMatType()->getType() ?></type>
            <defaultsel><?php echo print_boolean($mat->getMatType()->getDefaultsel()) ?></defaultsel>
            <mimetype><?php echo $mat->getMatType()->getMimetype() ?></mimetype>
          </mattype>
        </material>
<?php endforeach;?>
      </materials>
      <links>
<?php foreach($mm->getLinks() as $link):?>
        <link rank="<?php echo $link->getRank() ?>" id="<?php echo $link->getId() ?>">
          <url><?php echo print_string($link->getUrl()) ?></url>
          <name>
<?php foreach($langs as $lang): $link->setCulture($lang); ?>
            <<?php echo $lang ?>><?php echo print_string($link->getName()) ?></<?php echo $lang ?>>
<?php endforeach;?>
          </name>
        </link>
<?php endforeach;?>
      </links>
<?php if($mh = MmMatterhornPeer::retrieveByPK($mm->getId())): ?>
      <opencast>
        <id><?php echo $mh->getMhId()?></id>
        <link><?php echo print_string($mh->getPlayerUrl())?></link>
        <invert><?php echo print_boolean($mh->getInvert())?></invert>
        <numview><?php echo $mh->getNumView() ?></numview>
        <duration><?php echo $mh->getDuration() ?></duration>
        <language><?php echo $mh->getLanguage()->getCod() ?></language>
        <display><?php echo print_boolean($mh->getDisplay()) ?></display>
      </opencast>
<?php endif ?>
    </mm>
<?php endforeach;?>
  </mms>
</serial>
<?php exit ?>