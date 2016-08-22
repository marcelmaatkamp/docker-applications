package showcase

import org.apache.commons.lang3.builder.*
import java.lang.reflect.Field;

class MyReflectionToStringBuilder extends ReflectionToStringBuilder {

  public MyReflectionToStringBuilder(final Object object) {
    super(object);

    StandardToStringStyle style = new StandardToStringStyle();
    style.setUseClassName(false);
    style.setUseIdentityHashCode(false);
    setDefaultStyle(style);

    setExcludeFieldNames("id","version","org_grails_datastore_gorm_GormValidateable__errors","org_grails_datastore_gorm_GormValidateable__skipValidate","transients","instanceControllersDomainBindingApi")
  }

}
