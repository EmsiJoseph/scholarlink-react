import * as React from 'react';
import * as MUI from '../../import'
import { Link } from 'react-router-dom';
import { DropdownMenu, DropdownMenu2, DropdownMenu3 } from '../../component/Layout/DropdownMenu/DropdownMenu';

const listItemStyles = {
  paddingLeft: '16px',
};

const subheaderStyles = {
  paddingLeft: '12px',
  backgroundColor: '#fbf3f2',
};

const hiddenOnMobile = {
  '@media (max-width: 600px)': {
    display: 'none',
  },
};


const CustomListItem = ({ to, icon, secondary, imageSrc, alt }) => (
  <MUI.ListItemButton component={Link} to={to} sx={{ ...listItemStyles }}>
    <MUI.ListItemIcon>{icon}</MUI.ListItemIcon>
    {imageSrc ? (
      <MUI.ListItemIcon>
        <MUI.Box
          component="img"
          sx={{
            height: 40,
            width: 60,
            marginLeft: '-20px',
          }}
          alt={alt}
          src={imageSrc}
        />
      </MUI.ListItemIcon>
    ) : null}
    <MUI.ListItemText secondary={secondary} sx={{ fontSize: '12px !important' }} />
  </MUI.ListItemButton>
);

{/* -------------------- SCHOLAR MANAGER PORTAL OPTION -------------------- */}
export const SMP_ListItems = (
  <React.Fragment>

    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Main
    </MUI.ListSubheader>
      <CustomListItem to="/" icon={<MUI.DashboardIcon />} secondary="Dashboard" />
  
    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Management
    </MUI.ListSubheader>
  
      <CustomListItem to="/scholar" icon={<MUI.PeopleAltOutlinedIcon />} secondary="Scholar" />
      <DropdownMenu/>
      <DropdownMenu3/>

    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Quick Actions
    </MUI.ListSubheader>

      <CustomListItem to="/create" icon={<MUI.AddBoxOutlinedIcon />} secondary="Create" />
      <CustomListItem to='/ask' 
        icon={<MUI.Box component='img' 
        sx={{height: 40, width: 60, marginLeft: '-20px'}} 
        alt="The house from the offer."
        src="https://raw.githubusercontent.com/TianMeds/image--stocks-for-coding/main/AI%20LOGO.png" /> } 
        secondary="Generate Report" 
      />

  </React.Fragment>
);

{/* -------------------- SCHOLAR ADMINISTRATOR PORTAL OPTION -------------------- */}

export const SAP_ListItems = (
  <React.Fragment>

    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Main
    </MUI.ListSubheader>
      <CustomListItem to="/" icon={<MUI.DashboardIcon />} secondary="Dashboard" />

    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Management
    </MUI.ListSubheader>

      <CustomListItem to="/user" icon={<MUI.GroupsOutlinedIcon />} secondary="Users" />
      <CustomListItem to="/scholar" icon={<MUI.PeopleAltOutlinedIcon />} secondary="Scholar" />
      <DropdownMenu/>
      <DropdownMenu3/>

    <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
      Quick Actions
    </MUI.ListSubheader>

      <CustomListItem to="/create" icon={<MUI.AddBoxOutlinedIcon />} secondary="Create" />
      <CustomListItem 
      to='/generate-report' 
      
      icon={<MUI.Box component='img' 
      sx={{height: 40, width: 60, marginLeft: '-20px'}} 
      alt="The house from the offer."
      src="https://raw.githubusercontent.com/TianMeds/image--stocks-for-coding/main/AI%20LOGO.png" />} 
      secondary="Generate Report" 
      />
  </React.Fragment>
  
);

{/* -------------------- SCHOLAR PORTAL OPTION -------------------- */}

export const SP_ListItems = (
  <React.Fragment>
  
  {/* -------------------- Main Drawer Option --------------------*/}
  <MUI.ListSubheader component="div" inset sx={{ ...subheaderStyles, ...hiddenOnMobile }}>
    Main
  </MUI.ListSubheader>

    <CustomListItem to="/scholar-dashboard" icon={<MUI.DashboardIcon />} secondary="Dashboard" />
    <DropdownMenu2/>
  

</React.Fragment>
);

