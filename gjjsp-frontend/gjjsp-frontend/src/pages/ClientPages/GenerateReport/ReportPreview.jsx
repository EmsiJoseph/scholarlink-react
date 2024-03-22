
import React, { useState, useEffect } from 'react';
import useLoginStore from '../../../store/LoginStore';

const ReportPreview = ({ reportData }) => {

    const { setLoading, setLoadingMessage} = useLoginStore();


    useEffect(() => {
        // When the component mounts and reportData is not yet available, show loading
        if (!reportData) {
          setLoading(true);
          setLoadingMessage("Generating Report, please wait...");
        } else {
          // Once the reportData is available, stop showing loading
          setLoading(false);
        }
    
        // Cleanup function to reset loading state when the component unmounts or reportData changes
        return () => {
          setLoading(false);
        };
      }, [reportData, setLoading, setLoadingMessage]);



  // Helper function to render a table
  const renderTable = (data, title) => {
  // Check if data is truthy and is an object or array; if not, return a placeholder or nothing
  if (!data || typeof data !== 'object' || Object.keys(data).length === 0) {
    return <p>No data available for {title}.</p>;
  }

  return (
    <>
      <h3>{title}</h3>
      <table border="1" style={{ width: '100%', borderCollapse: 'collapse' }}>
        <thead>
          <tr>
            {Object.keys(data[0]).map((key) => (
              <th key={key} style={{ border: '1px solid black', textAlign: 'center' }}>{key}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {data.map((row, index) => (
            <tr key={index}>
              {Object.values(row).map((val, idx) => (
                <td key={idx} style={{ border: '1px solid black', textAlign: 'center' }}>{val}</td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </>
  );
};


  return (
    <div>
      <div className="header">
        <h2>ASSISI DEVELOPMENT FOUNDATION INC.</h2>
        <h3>SPECIAL PROJECT- GADO & JESS JALANDONI SCHOLARSHIP PROJECT</h3>
        <h4>{reportData.reportType}</h4>
        <p>From {reportData.monthRange} {reportData.year}</p>
      </div>

      <h2>Areas of Coverage</h2>
      <p>Regions – {reportData.totalRegions}</p>
      <p>Provinces – {reportData.totalProvinces}</p>
      <p>Cities/Municipalities – {reportData.totalCitiesMunicipalities}</p>

      {reportData.regionData && renderTable(reportData.regionData, "Regions")}
      {reportData.provinceData && renderTable(reportData.provinceData, "Provinces")}
      {reportData.cityMunicipalityData && renderTable(reportData.cityMunicipalityData, "Cities/Municipalities")}

      <h2>Project Partners Report</h2>
      {reportData.projectPartnersData && Array.isArray(reportData.projectPartnersData) && reportData.projectPartnersData.map(([islandGroup, data]) => (
        <section key={islandGroup}>
            {renderTable(data, `${islandGroup} based Scholars`)}
        </section>
        ))}


    </div>
  );
};

export default ReportPreview;
