import React, { useState, useEffect } from 'react';
import * as MUI from '../../../import';
import Layout from '../../../component/Layout/SidebarNavbar/Layout';
import axios from '../../../api/axios';
import { useForm, Controller } from 'react-hook-form';
import theme from '../../../context/theme';

export default function GenerateReport() {

 
  const { control, watch, setValue, reset } = useForm({
    defaultValues: {
      reportType: '',
      year: '',
      currentYear: false,
    }
  });
 
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [confirmMessage, setConfirmMessage] = useState('');
  const [currentYear, setCurrentYear] = useState(new Date().getFullYear());
  const [yearOptions, setYearOptions] = useState([]);


  
  // Get current year with Philippine timezone (replace with your library)
  useEffect(() => {
    const philippineDate = new Date().toLocaleString('ph-PH', { timeZone: 'Asia/Manila' });
    setCurrentYear(new Date(philippineDate).getFullYear());
  }, []);

  

    // Generate year options from 1990 to the current year
    useEffect(() => {
      setYearOptions(
        Array.from({ length: currentYear - 1989 }, (_, index) => 1990 + index)
      );
    }, [currentYear]);

    // Function to get date range based on report type and year
  const getDateRange = (reportType, year) => {
    let startDate, endDate;
    switch (reportType) {
      case '1':
        startDate = `${year}-01-01`;
        endDate = `${year}-06-30`;
        break;
      case '2':
        startDate = `${year}-07-01`;
        endDate = `${year}-12-31`;
        break;
      // Case '3' is handled differently since it generates two reports
    }
    return { startDate, endDate };
  };

  const handleClear = () => {
    reset({
      reportType: '',
      year: '',
      currentYear: false,
    });
  };

  const handleGenerateClick = (event) => {
    event.preventDefault();
    const formData = watch();
    const year = formData.currentYear ? String(currentYear) : formData.year;
  
    let message;
    // Determine the message to display in the confirmation modal based on the selected options
    if (formData.reportType === '3') {
      // Message for generating both Mid-year and Year-end reports
      message = `Both Mid-year and Year-end Accomplishment Reports for ${formData.currentYear ? 'the current school year' : `${year} period`} will be generated. Are you sure?`;
    } else {
      // Message for generating either Mid-year or Year-end report
      let reportTypeMessage = formData.reportType === '1' ? 'Mid-year' : 'Year-end';
      message = `${reportTypeMessage} Accomplishment Report ${formData.currentYear ? `${year}` : `${year}`} will be generated. Are you sure?`;
    }
  
    setConfirmMessage(message);
    setConfirmOpen(true);
  };

  const handleConfirmGenerateReport = async () => {
    // Close the confirmation modal first
    setConfirmOpen(false);
  
    const formData = watch(); // Make sure to retrieve the form data again if needed
    const year = formData.currentYear ? String(currentYear) : formData.year;
  
    if (formData.reportType === '3') {
      // Generate both Mid-year and Year-end reports
      const midYearRange = getDateRange('1', year);
      const yearEndRange = getDateRange('2', year);
      await generateReport(midYearRange);
      await generateReport(yearEndRange);
    } else {
      // Generate a single report, either Mid-year or Year-end
      const dateRange = getDateRange(formData.reportType, year);
      await generateReport(dateRange);
    }
  };
  
  
  const generateReport = async ({ startDate, endDate, reportType }) => {
    // Placeholder: Replace with your actual data fetching and report generation logic
    console.log(`Generating report of type ${reportType} for the period from ${startDate} to ${endDate}.`);

  
    // Fetching data from the backend
    try {
      const response = await axios.get('/api/report-data', {
        params: { startDate, endDate, reportType }
      });
      // Assuming the response includes the data needed for the report
      const reportData = response.data;
  
      // Here you would format the reportData as needed for the report
  
      // Finally, trigger download of the report or display it in the UI
      console.log('Report generated:', reportData);
    } catch (error) {
      console.error('Error generating report:', error);
    }
  };
  


  return (
    <Layout>
      <MUI.ThemeProvider theme={theme}>
        <MUI.Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
          <MUI.Grid container spacing={3}>
          <MUI.Grid item xs={12} mb={4}>
          <MUI.Typography variant="h1" id="tabsTitle" sx={{color: 'black'}}>Generate Report</MUI.Typography>
          </MUI.Grid>
            <MUI.Grid item xs={12}>
              
              <MUI.Paper sx={{ p: 4, maxWidth: '600px', width: '100%', mx: 'auto' }}>
                <form onSubmit={handleGenerateClick}>
                  <MUI.Grid container spacing={3}>

                    {/* Title Section */}
                    <MUI.Grid item xs={12}>
                    <MUI.Typography variant="h4" sx={{ fontWeight: 'bold', mb: 2 }}>
                        Accomplishment Report
                      </MUI.Typography>

                    </MUI.Grid>


                    {/* Report Type Selection */}
                    <MUI.Grid item xs={12}>
                      <MUI.FormControl fullWidth>
                        <MUI.InputLabel id="report-type-label">Report Type *</MUI.InputLabel>
                        <Controller
                          name="reportType"
                          control={control}
                          rules={{ required: 'Report type is required' }}
                          render={({ field, fieldState }) => (
                            <MUI.Select
                              labelId="report-type-label"
                              label="Report Type *"
                              {...field}
                            >
                              <MUI.MenuItem value="1">Mid-year Accomplishment Report</MUI.MenuItem>
                              <MUI.MenuItem value="2">Year-end Accomplishment Report</MUI.MenuItem>
                              <MUI.MenuItem value="3">Both types</MUI.MenuItem>
                            </MUI.Select>
                          )}
                        />
                      </MUI.FormControl>
                    </MUI.Grid>

                    {/* Year Selection */}
                    <MUI.Grid item xs={12}>
                      <Controller
                        name="year"
                        control={control}
                        rules={{ required: !watch('currentYear') }}
                        render={({ field, fieldState }) => (
                          <MUI.FormControl fullWidth error={!!fieldState.error}>
                            <MUI.InputLabel id="year-label">Year *</MUI.InputLabel>
                            <MUI.Select
                              labelId="year-label"
                              label="Year *"
                              {...field} // This spreads the Controller's field props directly
                              disabled={watch('currentYear')}
                            >
                              {yearOptions.map((year) => (
                                <MUI.MenuItem key={year} value={year}>
                                  {year}
                                </MUI.MenuItem>
                              ))}
                            </MUI.Select>
                            <MUI.FormHelperText>
                              {fieldState.error ? fieldState.error.message : null}
                            </MUI.FormHelperText>
                          </MUI.FormControl>
                        )}
                      />
                    </MUI.Grid>

                            {/* Current Year Checkbox */}
                            
              <MUI.Grid item xs={12}>
              <Controller
                name="currentYear"
                control={control}
                render={({ field }) => (
                  <MUI.FormControlLabel
                    control={
                      <MUI.Checkbox
                        {...field}
                        checked={field.value} // This should be just the boolean value
                        onChange={(e) => {
                          const isChecked = e.target.checked;
                          field.onChange(isChecked); // Update the checkbox's form value
                          setValue('year', isChecked ? String(currentYear) : ''); // Update the year in the form based on the checkbox
                        }}
                      />
                    }
                    label="Current Year"
                  />
                )}
              />

              </MUI.Grid>



                    {/* Buttons */}
                    <MUI.Grid item xs={12}>
                      <MUI.Box display="flex" justifyContent="flex-end">
                      <MUI.Button
                        onClick={handleClear}
                        disabled={!watch('reportType') || (!watch('currentYear') && !watch('year'))}
                      >
                        Clear
                      </MUI.Button>
                      <MUI.Button
                        variant="contained"
                        color="primary"
                        onClick={handleGenerateClick}
                        sx={{ mt: 3, mb: 2 }}
                        disabled={!watch('reportType') || (!watch('currentYear') && !watch('year'))}
                      >
                        Generate Report
                      </MUI.Button>

                     
                        {/* ---------------- Confirmation dialog ---------------- */}
                       
                        <MUI.Dialog open={confirmOpen} onClose={() => setConfirmOpen(false)}>
                          <MUI.DialogTitle id="dialogTitle" mt={2}>{'Confirm Generate'}</MUI.DialogTitle>
                                    <MUI.DialogContent>
                                      <MUI.Typography variant='h5' ml={1} sx={{color: '#44546F'}}>
                                      {confirmMessage}
                                      </MUI.Typography>
                                    </MUI.DialogContent>
                                    <MUI.DialogActions>
                                      
                              <MUI.Button onClick={() => setConfirmOpen(false)} color="primary">Cancel</MUI.Button>
                              
                            <MUI.Button onClick={() => {
                              // Call the export function
                              handleConfirmGenerateReport;
                              // Close the modal
                              setConfirmOpen(false);
                            }} color="primary" variant="contained"
                            type='submit' 
                            sx={{backgroundColor: '#0C66E4', borderRadius: '5px', mb: 2, mt: 2 }}>
                              Yes, Generate now
                            </MUI.Button>
                          </MUI.DialogActions>
                        </MUI.Dialog>



                      </MUI.Box>
                    </MUI.Grid>
                  </MUI.Grid>
                </form>
              </MUI.Paper>
            </MUI.Grid>
          </MUI.Grid>
        </MUI.Container>
      </MUI.ThemeProvider>
    </Layout>
  );
}